<?php
/**
 * Core Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include core functions (available in both admin and frontend).
require MEMBERHERO_ABSPATH . 'includes/memberhero-conditional-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-formatting-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-date-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-upload-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-form-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-field-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-role-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-list-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-page-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-account-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-profile-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-user-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-avatar-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-cover-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-dropdown-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-locale-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-cron-functions.php';
require MEMBERHERO_ABSPATH . 'includes/memberhero-widget-functions.php';

/**
 * Returns true when we're displaying a loop.
 */
function memberhero_is_in_loop() {
	global $the_list;

	$in_loop = false;

	if ( isset( $the_list->_in_loop ) && $the_list->_in_loop == true ) {
		$in_loop = true;
	}

	return apply_filters( 'memberhero_is_in_loop', $in_loop );
}

/**
 * Return a list of plugin specific post types.
 */
function memberhero_get_post_types() {
	return apply_filters( 'memberhero_get_post_types', array( 'memberhero_form', 'memberhero_field', 'memberhero_role', 'memberhero_list' ) );
}

/**
 * Define a constant if it is not already defined.
 */
function memberhero_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}

/**
 * Return the html selected attribute if stringified $value is found in array of stringified $options
 * or if stringified $value is the same as scalar stringified $options.
 */
function memberhero_selected( $value, $options ) {
	if ( is_array( $options ) ) {
		$options = array_map( 'strval', $options );
		return selected( in_array( (string) $value, $options, true ), true, false );
	}

	return selected( $value, $options, false );
}

/**
 * Display a SVG icon from the sprite.
 */
function memberhero_svg_icon( $icon = '' ) {
	$html = '<svg class="feather"><use xlink:href="'. memberhero()->plugin_url() . '/assets/images/feather-sprite.svg#' . esc_html( $icon ) . '" /></svg>';

	// can be used for custom icon output.
	return apply_filters( 'memberhero_svg_icon_html', $html, $icon );
}

/**
 * Display a help tip.
 */
function memberhero_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = memberhero_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="memberhero-help-tip" data-tip="' . $tip . '">' . memberhero_svg_icon( 'help-circle' ) . '</span>';
}

/**
 * Get template part.
 */
function memberhero_get_template_part( $slug, $name = '' ) {
	global $the_form, $the_user, $the_list, $logged_user;

	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/memberhero/slug-name.php.
	if ( $name && ! MEMBERHERO_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}-{$name}.php", memberhero()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php.
	if ( ! $template && $name && file_exists( memberhero()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = memberhero()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/memberhero/slug.php.
	if ( ! $template && ! MEMBERHERO_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}.php", memberhero()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'memberhero_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 */
function memberhero_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	global $the_form, $the_user, $the_list, $logged_user;

	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = memberhero_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'memberhero_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'memberhero_before_template_part', $template_name, $template_path, $located, $args );

	include $located;

	do_action( 'memberhero_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Like memberhero_get_template, but returns the HTML instead of outputting.
 */
function memberhero_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	global $the_form, $the_user, $the_list, $logged_user;

	ob_start();
	memberhero_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 */
function memberhero_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	global $the_form, $the_user, $the_list, $logged_user;

	if ( ! $template_path ) {
		$template_path = memberhero()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = memberhero()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template/.
	if ( ! $template || MEMBERHERO_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'memberhero_locate_template', $template, $template_name, $template_path );
}

/**
 * Get scope for viewed form. Should be edit or view.
 */
function memberhero_get_scope() {
	global $the_form;

	$scope = 'edit';

	if ( $the_form->type == 'profile' ) {
		// only enable editing profile forms when edit mode is turned on.
		if ( get_query_var( 'memberhero_tab' ) != 'edit' ) {
			$scope = 'view';
		}
	}

	return apply_filters( 'memberhero_get_scope', $scope );
}

/**
 * Sort values based on ascii, usefull for special chars in strings.
 */
function memberhero_ascii_uasort_comparison( $a, $b ) {
	if ( function_exists( 'iconv' ) && defined( 'ICONV_IMPL' ) && @strcasecmp( ICONV_IMPL, 'unknown' ) !== 0 ) {
		$a = @iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $a );
		$b = @iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $b );
	}
	return strcmp( $a, $b );
}

/**
 * Array insert before a key.
 */
function memberhero_array_insert_before( $key, array &$array, $new_key, $new_value ) {
	if ( array_key_exists( $key, $array ) ) {
		$new = array();
		foreach ( $array as $k => $value ) {
			if ( $k === $key ) {
				$new[$new_key] = $new_value;
			}
			$new[$k] = $value;
		}
		return $new;
	}
	return false;
}

/**
 * Array insert after a key.
 */
function memberhero_array_insert_after( $key, array &$array, $new_key, $new_value ) {
	if ( array_key_exists( $key, $array ) ) {
		$new = array();
		foreach ( $array as $k => $value ) {
			$new[$k] = $value;
			if ( $k === $key ) {
				$new[$new_key] = $new_value;
			}
		}
		return $new;
	}
	return false;
}

/**
 * Search array and get first matched.
 */
function memberhero_array_search( $search, $array ){
    foreach( $array as $item ) {
		foreach( $item as $key => $val ) {
			if ( is_array( $val ) ) {
				foreach( $val as $metakey => $metavalue ) {
					if ( $metavalue == $search ) {
						return $item;
					}
				}
			}
		}
    }
	return false;
}

/**
 * Get maximum value for a column in an array.
 */
function memberhero_get_max_column( $array, $column ) {
	$counts = array();
	if ( $array ) {
		foreach( $array as $key => $value ) {
			if ( isset( $value[ $column ] ) ) {
				$counts[] = $value[ $column ];
			}
		}
		return $max = max( $counts );
	}
	return 1;
}

/**
 * Get rating hints.
 */
function memberhero_get_rating_hints( $field = null ) {

	$defaults = apply_filters( 'memberhero_get_default_rating_hints', array(
		__( 'Poor', 'memberhero' ),
		__( 'Below Average', 'memberhero' ),
		__( 'Average', 'memberhero' ),
		__( 'Good', 'memberhero' ),
		__( 'Excellent', 'memberhero' ),
	) );

	if ( ! empty( $field[ 'ratings' ] ) ) {
		$ratings = memberhero_comma_separated_string( $field[ 'ratings' ] );
	} else {
		$ratings = implode( ',', $defaults );
	}

	return apply_filters( 'memberhero_get_rating_hints', $ratings );
}

/**
 * Get a rating hint based on score.
 */
function memberhero_get_rating_hint( $score = 0, $field = null ) {
	if ( ! $score ) {
		return;
	}
	$hints = memberhero_get_rating_hints( $field );
	$hints = explode( ',', $hints );

	return apply_filters( 'memberhero_get_rating_hint', $hints[ $score - 1 ], $score, $field );
}

/**
 * Outputs a "back" link so admin screens can easily jump back a page.
 */
function memberhero_back_link( $label, $url ) {
	echo '<small class="memberhero-admin-breadcrumb"><a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $label ) . '">' . __( '&#8592; Back', 'memberhero' ) . '</a></small>';
}

/**
 * Queue some JavaScript code to be output in the footer.
 */
function memberhero_enqueue_js( $code ) {
	global $memberhero_queued_js;

	if ( empty( $memberhero_queued_js ) ) {
		$memberhero_queued_js = '';
	}

	$memberhero_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function memberhero_print_js() {
	global $memberhero_queued_js;

	if ( ! empty( $memberhero_queued_js ) ) {
		// Sanitize.
		$memberhero_queued_js = wp_check_invalid_utf8( $memberhero_queued_js );
		$memberhero_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $memberhero_queued_js );
		$memberhero_queued_js = str_replace( "\r", '', $memberhero_queued_js );

		$js = "<!-- Member Hero JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $memberhero_queued_js });\n</script>\n";

		echo apply_filters( 'memberhero_queued_js', $js ); // WPCS: XSS ok.

		unset( $memberhero_queued_js );
	}
}

/**
 * Switch to site language.
 */
function memberhero_switch_to_site_locale() {
	if ( function_exists( 'switch_to_locale' ) ) {
		switch_to_locale( get_locale() );

		// Filter on plugin_locale so load_plugin_textdomain loads the correct locale.
		add_filter( 'plugin_locale', 'get_locale' );

		// Init locale.
		memberhero()->load_plugin_textdomain();
	}
}

/**
 * Switch language to original.
 */
function memberhero_restore_locale() {
	if ( function_exists( 'restore_previous_locale' ) ) {
		restore_previous_locale();

		// Remove filter.
		remove_filter( 'plugin_locale', 'get_locale' );

		// Init locale.
		memberhero()->load_plugin_textdomain();
	}
}

/**
 * Checks if a provided token is valid.
 */
function memberhero_is_invalid_token( $key, $stored ) {
	return empty( $stored[ 'token' ] ) || empty( $stored[ 'expiry' ] ) || $stored[ 'token' ] !== $key || ( $stored[ 'expiry' ] - time() <= 0 );
}

/**
 * Same as wp_get_schedules() but orders the schedules by interval.
 */
function memberhero_get_schedules() {
	$schedules = wp_get_schedules();

	uasort( $schedules, function( $a, $b ) {
		return $a[ 'interval' ] - $b[ 'interval' ];
	} );

	$schedules = array_merge( array_combine( array_keys( $schedules ), array_column( $schedules, 'display' ) ), array( '' => __( 'Never', 'memberhero' ) ) );

	return apply_filters( 'memberhero_get_schedules', $schedules );
}

/**
 * Generate a rand hash.
 */
function memberhero_rand_hash() {
	if ( ! function_exists( 'openssl_random_pseudo_bytes' ) ) {
		return sha1( wp_rand() );
	}

	return bin2hex( openssl_random_pseudo_bytes( 20 ) );
}

/**
 * MC API - Hash.
 */
function memberhero_api_hash( $data ) {
	return hash_hmac( 'sha256', $data, 'memberhero-api' );
}

/**
 * Checks if email or domain is blocked.
 */
function memberhero_email_blocked( $email, $data ) {

	if ( ! empty( $data['blocked_emails'] ) && is_email( $email ) ) {
		$email_parts = explode( '@', $email );
		$user		 = trim( $email_parts[0] );
		$domain 	 = trim( $email_parts[1] );

		$values = explode( '+', $data['blocked_emails'] );
		foreach( $values as $value ) {
			if ( substr( $value, 0, 1 ) === '*' ) {
				if ( strstr( $value, $domain ) ) {
					return true;
					break;
				}
			} elseif ( ! strstr( $value, '@' ) ) {
				if ( strstr( $value, str_replace( '@', '', $domain ) ) ) {
					return true;
					break;
				}
			} elseif ( $value == $email ) {
				return true;
				break;
			} elseif ( substr( $value, 0, strlen( $user ) ) == $user && strstr( $value, '@*' ) ) {
				return true;
				break;
			}
		}
	} else if ( ! empty( $data['allowed_emails'] ) && is_email( $email ) ) {
		$email_parts = explode( '@', $email );
		$user		 = trim( $email_parts[0] );
		$domain 	 = trim( $email_parts[1] );

		$values = explode( '+', $data['allowed_emails'] );
		foreach( $values as $value ) {
			if ( substr( $value, 0, 1 ) === '*' ) {
				if ( strstr( $value, $domain ) ) {
					return false;
					break;
				}
			} elseif ( ! strstr( $value, '@' ) ) {
				if ( strstr( $value, str_replace( '@', '', $domain ) ) ) {
					return false;
					break;
				}
			} elseif ( $value == $email ) {
				return false;
				break;
			} elseif ( substr( $value, 0, strlen( $user ) ) == $user && strstr( $value, '@*' ) ) {
				return false;
				break;
			}
		}
		return true;
	}

	return false;
}

/**
 * Reset form data.
 */
function memberhero_reset_form_data() {
	global $the_form;

	$the_form = null;
}

/**
 * Reset user data.
 */
function memberhero_reset_user() {
	global $the_user;

	$the_user = null;
}