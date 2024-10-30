<?php
/**
 * Forms Core.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MemberHero_Abstract_Post', false ) ) {
	include_once 'abstracts/abstract-class-memberhero-post.php';
}

/**
 * MemberHero_Form class.
 */
class MemberHero_Form extends MemberHero_Abstract_Post {

	/**
	 * Post type.
	 */
	public $post_type = 'memberhero_form';

	/**
	 * Meta keys.
	 */
	public $internal_meta_keys = array(
		'type',
		'role',
		'rows',
		'fields',
		'row_count',
		'cols',
		'use_ajax',
		'force_role',
		'icons',
		'endpoint',
		'redirect',
		'redirect_uri',
		'show_cover',
		'show_menu',
		'show_members_menu',
		'show_social',
		'aligncenter',
		'confirm_email',
		'confirm_password',
	);

	/**
	 * Password generated.
	 */
	public $password_generated = null;

	/**
	 * Holds the saved query parameter value.
	 */
	public $saved = 'true';

	/**
	 * Define if the form needs to be cleared.
	 */
	public $cleardata = null;

	/**
	 * Is custom?
	 */
	public $is_custom = false;

	public $js_replace = '';

	/**
	 * Get form endpoint.
	 */
	public function get_endpoint() {
		$endpoint = $this->endpoint;

		if ( null == $endpoint ) {
			$endpoint = $this->type;
		}

		return wp_unslash( $endpoint );
	}

	/**
	 * Does the form have fields?
	 */
	public function has_fields() {
		if ( ! empty( $this->fields ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Get fields per specific row.
	 */
	public function fields_in( $row = 0, $col = 0 ) {
		if ( empty( $this->fields ) ) {
			return;
		}

		// Fix column start at zero.
		$col = $col + 1;

		// Allow output to be filtered.
		$this->fields = apply_filters( 'memberhero_get_form_fields', $this->fields, $this );

		return array_filter( ( array ) $this->fields, function( $val ) use ($row, $col) {
			return ( $val['row'] == absint( $row ) && $val['col'] == $col );
		} );
	}

	/**
	 * Custom save action.
	 */
	public function _save( $props ) {
		if ( ! empty( $props[ 'endpoint' ] ) ) {
			if ( $props[ 'endpoint' ] !== $this->endpoint ) {
				delete_option( 'memberhero_account_' . memberhero_sanitize_title( $this->endpoint ) . '_form' );
			}
			update_option( 'memberhero_account_' . memberhero_sanitize_title( $props[ 'endpoint' ] ) . '_form', $this->id );
		}

		// Save the role.
		if ( isset( $props[ 'type' ] ) && $props[ 'type' ] == 'profile' ) {
			if ( isset( $props[ 'role' ] ) && $props[ 'role' ] != $this->role ) {
				delete_option( 'memberhero_profile_form_' . $this->role );
			}
			if ( isset( $props[ 'force_role' ] ) && $props[ 'force_role' ] == 'yes' && $props[ 'role' ] ) {
				update_option( 'memberhero_profile_form_' . $props[ 'role' ], $this->id );
			}
		}
	}

	/**
	 * Get the role title.
	 */
	public function get_role_title() {
		$role  = $this->role;
		$roles = memberhero_get_roles();

		return isset( $roles[ $role ] ) ? $roles[ $role ] : '';
	}

	/**
	 * Fires redirect after profile edit.
	 */
	public function profile_redirect( $user = null ) {
		if ( empty( $user ) ) {
			$user = memberhero_get_active_profile_id();
		}

		$redirect = add_query_arg( array( 'saved' => $this->saved ), memberhero_get_profile_url( $user ) );

		return $this->get_redirect( $redirect );
	}

	/**
	 * Fires redirect after account edit.
	 */
	public function account_redirect() {
		$redirect = add_query_arg( array( 'saved' => $this->saved ), memberhero_get_account_endpoint_url( $this->get_endpoint() ) );

		return $this->get_redirect( $redirect );
	}

	/**
	 * Get the redirect depending on type of request. AJAX or standard.
	 */
	public function get_redirect( $redirect ) {
		if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->js_redirect = $redirect;
		} else {
			// Should we use wp_safe_redirect? For now, we use this to allow external redirection.
			wp_redirect( $redirect );
			exit;
		}
	}

	/**
	 * Get form fields.
	 */
	public function get_fields() {
		return apply_filters( 'memberhero_get_form_fields', $this->fields, $this );
	}

	/**
	 * Validation of user input.
	 */
	public function validate() {
		if ( empty( $this->get_fields() ) ) {
			return;
		}

		do_action( 'memberhero_pre_validate_form' );

		foreach( $this->get_fields() as $key => $array ) {
			$this->validate_input( $array['data'] );
		}

		// Add custom data via 3rd party or not registered in core form.
		do_action( 'memberhero_add_custom_data', $this );

		// Add custom validation for a specific form type.
		do_action( 'memberhero_' . $this->type . '_validation', memberhero_form_get_postdata( $this->id ) );

		// Add custom validation for a specific endpoint.
		if ( $this->endpoint ) {
			do_action( 'memberhero_' . memberhero_sanitize_title( $this->endpoint ) . '_validation', memberhero_form_get_postdata( $this->id ) );
		}

		// Post validation.
		do_action( 'memberhero_post_validate_form' );
	}

	/**
	 * Default validation.
	 */
	public function validate_input( $data ) {
		extract( $data );

		$thedata = memberhero_form_get_postdata( $this->id );

		$update = true;
		$input  = isset( $this->id ) && ! empty( $this->id ) ? 'memberheroform' . $this->id . '_' . $key : $key;
		$value  = ! isset( $_REQUEST[ $input ] ) ? '' : $_REQUEST[ $input ];

		// Checks based on key.
		switch( $key ) {
			case 'user_login' :
				if ( ! $value ) {
					if ( $this->type == 'register' ) {
						memberhero_add_notice( __( 'Please enter a username', 'memberhero' ), 'error', $key );
					} elseif ( $this->type == 'login' ) {
						memberhero_add_notice( __( 'Please enter your username', 'memberhero' ), 'error', $key );
					}
				}
			break;
			case 'user_pass' :
				if ( ! $value ) {
					if ( $this->type == 'register' ) {
						memberhero_add_notice( __( 'Please enter a password', 'memberhero' ), 'error', $key );
					} elseif ( $this->type == 'login' ) {
						memberhero_add_notice( __( 'Please enter your password', 'memberhero' ), 'error', $key );
					}
				}
			break;
			case 'user_email' :
				if ( ! $value ) {
					memberhero_add_notice( __( 'Please enter your email', 'memberhero' ), 'error', $key );
				}
			break;
			case 'role' :
				if ( ! empty( $value ) ) {
					if ( apply_filters( 'memberhero_prevent_admin_role_in_user_input', true ) && in_array( $value, memberhero_get_admin_roles() ) ) {
						memberhero_add_notice( __( 'Security issue - we cannot register you as admin', 'memberhero' ), 'error', $key );
					}
				}
			break;
			case 'confirm_user_email' :
				if ( ! $value ) {
					memberhero_add_notice( __( 'Please confirm your email', 'memberhero' ), 'error', $key );
				} elseif ( $value && $value !== $thedata[ 'user_email' ] ) {
					memberhero_add_notice( __( 'Your email does not match', 'memberhero' ), 'error', $key );
				}
			break;
			case 'confirm_user_pass' :
				if ( ! $value ) {
					memberhero_add_notice( __( 'Please re-enter a password', 'memberhero' ), 'error', 'user_pass' );
					memberhero_add_notice( __( 'Please confirm your password', 'memberhero' ), 'error', $key );
				} elseif ( $value && $value !== $thedata[ 'user_pass' ] ) {
					memberhero_add_notice( __( 'Please re-enter a password', 'memberhero' ), 'error', 'user_pass' );
					memberhero_add_notice( __( 'Your password does not match', 'memberhero' ), 'error', $key );
				}
			break;
		}

		// Checks based on type.
		switch( $type ) {
			// Email validation.
			case 'email' :
				if ( ! is_email( trim( $value ) ) && ! empty( $value ) ) {
					memberhero_add_notice( __( 'Please enter a valid email', 'memberhero' ), 'error', $key );
				}
				$value = sanitize_email( $value );
				// Check if email is blocked.
				if ( memberhero_email_blocked( $value, $data ) ) {
					memberhero_add_notice( __( 'You are not allowed to use this email', 'memberhero' ), 'error', $key );
				}
			break;
			// Phone validation.
			case 'phone' :
				if ( ! empty( $value ) && preg_match( '/[a-z]/i', $value ) ) {
					memberhero_add_notice( __( 'Invalid phone number entered', 'memberhero' ), 'error', $key );
				}
				$value = memberhero_clean( wp_unslash( $value ) );
			break;
			// Number validation.
			case 'number' :
				if ( ! empty( $value ) ) {
					if ( ! empty( $enable_decimals ) ) {
						if ( ! is_numeric( $value ) ) {
							memberhero_add_notice( sprintf( __( '%s - Please enter a valid nubmer', 'memberhero' ), $label ), 'error', $key );
						}
					} else {
						if ( ! ctype_digit( $value ) ) {
							memberhero_add_notice( sprintf( __( '%s - Please enter a valid nubmer', 'memberhero' ), $label ), 'error', $key );
						}
					}
					if ( ! empty( $min_num ) && empty( $max_num ) ) {
						if ( $value < $min_num ) {
							memberhero_add_notice( sprintf( __( '%s - A minimum value of %s is required', 'memberhero' ), $label, $min_num ), 'error', $key );
						}
					} else if ( ! empty( $max_num ) && empty( $min_num ) ) {
						if ( $value > $max_num ) {
							memberhero_add_notice( sprintf( __( '%s - A maximum value of %s can be entered', 'memberhero' ), $label, $max_num ), 'error', $key );
						}
					} else if ( ! empty( $min_num ) && ! empty( $max_num ) ) {
						if ( $value > $max_num || $value < $min_num ) {
							memberhero_add_notice( sprintf( __( '%s - Please enter a value between %s and %s', 'memberhero' ), $label, $min_num, $max_num ), 'error', $key );
						}
					}
				}
			break;
			// Toggle validation.
			case 'toggle' :
				$value = isset( $_REQUEST[ $input ] ) ? 'yes' : 'no';
			break;
			// Image and file validation.
			case 'file' :
			case 'image' :
				$value = $this->add_file_metadata( $data );
				if ( empty( $value ) ) {
					$update = false;
				}
			break;
		}

		// Checks based on form type.
		switch( $this->type ) {
			case 'register' :
				if ( $key == 'user_email' ) {
					if ( $user_id = email_exists( $value ) ) {
						// Email exists but have not confirmed. Try to send a new code.
						if ( memberhero_user_email_unconfirmed( $value ) ) {
							$user = get_userdata( $user_id );

							$confirmation_code = MemberHero_Shortcode_Form_Register::create_confirmation_code( $user );

							wp_safe_redirect( add_query_arg( array( 'checkmail' => 'true' ) ) );
							exit;
						}
						memberhero_add_notice( __( 'Email has already been taken.', 'memberhero' ), 'error', $key );
					}
				}
				if ( $key == 'user_login' ) {
					if ( username_exists( $value ) ) {
						memberhero_add_notice( __( 'Username has already been taken.', 'memberhero' ), 'error', $key );
					}
					if ( validate_username( $value ) == false ) {
						memberhero_add_notice( __( 'Your username can have letters, numbers and underscore.', 'memberhero' ), 'error', $key );
					}
				}
				break;
			default :
				// For 3rd party integrations.
				do_action( 'memberhero_custom_validate_' . $this->type, $key, $value, $type );
				break;
		}

		// Check if required.
		if ( ! $value && ! empty( $is_required ) ) {
			if ( ! empty( $custom_error ) ) {
				memberhero_add_notice( $custom_error, 'error', $key );
			} elseif ( ! empty( $label ) ) {
				memberhero_add_notice( sprintf( __( '%s is required.', 'memberhero' ), $label ), 'error', $key );
			} else {
				memberhero_add_notice( __( 'An error has occurred.', 'memberhero' ), 'error', $key );
			}
		}

		// Add custom validation for a specific key.
		do_action( 'memberhero_validate_input_' . $key, $data );

		// Add the key/value to the final update array.
		if ( apply_filters( 'memberhero_validate_input_flag', $update, $key, $data, $this->id ) ) {
			memberhero_form_add_postdata( $this->id, $key, $value );
		}
	}

	/**
	 * Add file metadata.
	 */
	public function add_file_metadata( $data ) {
		extract( $data );

		$array = array();

		if ( isset( $_FILES[ $key ] ) && $_FILES[ $key ]['size'] > 0 && $_FILES[ $key ]['error'] == 0 ) {
			if ( ! empty( $data['mimes'] ) && $data['type'] == 'file' ) {
				$data['mimes'] = explode( ',', $data['mimes'] );
				$mimes = get_allowed_mime_types();
				foreach( $data['mimes'] as $index => $mime ) {
					unset( $data['mimes'][ $index ] );
					if ( isset( $mimes[ $mime ] ) ) {
						$data['mimes'][ $mime ] = $mimes[ $mime ];
					}
				}
			}
			$array = array_merge( $_FILES[ $key ], array( 'field' => $data ) );
		}

		return $array;
	}

	/**
	 * Get secondary button url.
	 */
	public function get_second_button_url( $url = null ) {
		switch( $this->type ) :
			case 'login' :
				$url = memberhero_register_url();
			break;
			case 'register' :
			case 'lostpassword' :
				$url = memberhero_login_url();
			break;
			default :
			break;
		endswitch;

		return apply_filters( 'memberhero_secondary_button_url', $url, $this );
	}

	/**
	 * Add custom form support.
	 */
	public function add_custom( $type, $hidden_fields = array() ) {
		$this->type = memberhero_clean( wp_unslash( $type ) );
		$method = "get_custom_{$this->type}_fields";

		if ( method_exists( $this, $method ) ) {
			$this->$method();
		} else {

			// Allow plugins and 3rd party to add their custom fields.
			do_action( 'memberhero_' . $this->type . '_custom_fields' );
		}

		// Add hidden inputs.
		if ( ! empty( $hidden_fields ) ) {
			$this->hidden_fields = $hidden_fields;
		}

		$this->is_custom = true;
	}

	/**
	 * Get password reset custom fields.
	 */
	public function get_custom_password_reset_fields() {
		unset( $this->fields );
		$this->fields[] = array(
			'data' 	=> array(
				'key'			=> 'password_1',
				'label'			=> __( 'New password', 'memberhero' ),
				'type'			=> 'password',
				'hide_toggle' 	=> true,
			),
			'row'  	=> 1,
			'col'	=> 1,
		);

		$this->fields[] = array(
			'data' 	=> array(
				'key'			=> 'password_2',
				'label'			=> __( 'Confirm new password', 'memberhero' ),
				'type'			=> 'password',
				'hide_toggle' 	=> true,
			),
			'row'  	=> 1,
			'col'	=> 1,
		);
	}

}