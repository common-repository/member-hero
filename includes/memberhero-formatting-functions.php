<?php
/**
 * Formatting Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 */
function memberhero_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'memberhero_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Cleans a string and return a comma separated string.
 */
function memberhero_comma_separated_string( $string ) {
	return memberhero_clean( trim( str_replace( ', ', ',', str_replace( ' ,', ',', $string ) ) ) );
}

/**
 * Clean lowercase variables and use underscore instead of dash.
 */
function memberhero_sanitize_title( $var ) {
	return str_replace( '-', '_', sanitize_title( wp_unslash( $var ) ) );
}

/**
 * Clean a display name so that we can use as a username.
 */
function memberhero_sanitize_name( $name ) {
	return str_replace( '_', '.', memberhero_sanitize_title( $name ) );
}

/**
 * Sanitize a string destined to be a tooltip.
 */
function memberhero_sanitize_tooltip( $var ) {
	return htmlspecialchars(
		wp_kses(
			html_entity_decode( $var ), array(
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'small'  => array(),
				'span'   => array(),
				'ul'     => array(),
				'li'     => array(),
				'ol'     => array(),
				'p'      => array(),
			)
		)
	);
}

/**
 * Make a string lowercase.
 * Try to use mb_strtolower() when available.
 */
function memberhero_strtolower( $string ) {
	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string ) : strtolower( $string );
}

/**
 * Escape URL for frontend view.
 */
function memberhero_esc_url( $url ) {
	$disallowed = array( 'http://', 'https://' );
	foreach( $disallowed as $d ) {
		if ( strpos( $url, $d ) === 0 ) {
			return str_replace( $d, '', esc_url( untrailingslashit( $url ) ) );
		}
	}
	return esc_url( $url );
}

/**
 * Implode and escape HTML attributes for output.
 */
function memberhero_implode_html_attributes( $raw_attributes ) {
	$attributes = array();
	foreach ( $raw_attributes as $name => $value ) {
		$attributes[] = esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
	}
	return implode( ' ', $attributes );
}

/**
 * Allowed tags for textarea content.
 */
function memberhero_textarea_allowed_tags() {
	$allowed_tags = array();

	return apply_filters( 'memberhero_textarea_allowed_tags', $allowed_tags );
}

/**
 * Converts array to encoded string.
 */
function memberhero_array_to_encoded_str( $array ) {
	return rtrim( strtr( base64_encode( @gzdeflate( serialize( $array ), 9 ) ), '+/', '-_' ), '=' );
}

/**
 * Converts encoded string back to an array.
 */
function memberhero_encoded_str_to_array( $query ) {
	return unserialize( @gzinflate( base64_decode( strtr( memberhero_clean( $query ), '-_', '+/' ) ) ) );
}

/**
 * Convert seconds to hours.
 */
function memberhero_seconds_to_hours( $seconds ) {
	$hours = floor( $seconds / 3600 );

	return $hours > 1 ? $hours . ' hours ago' : '1 hour ago';
}

/**
 * Returns a more compact md5 hashing.
 */
function memberhero_md5( $string ) {
	return substr( base_convert( md5( $string ), 16, 32 ), 0, 12 );
}

/**
 * Generates a unique ID to use for file uploads.
 */
function memberhero_unique_filename() {
	return apply_filters( 'memberhero_unique_filename', memberhero_md5( uniqid() . microtime() . mt_rand() ) );
}

/**
 * Detect if we should use a light or dark color on a background color.
 */
function memberhero_light_or_dark( $color, $dark = '#202020', $light = '#FFFFFF' ) {
	return memberhero_hex_is_light( $color ) ? $dark : $light;
}

/**
 * Determine whether a hex color is light.
 */
function memberhero_hex_is_light( $color ) {
	$hex = str_replace( '#', '', $color );

	$c_r = hexdec( substr( $hex, 0, 2 ) );
	$c_g = hexdec( substr( $hex, 2, 2 ) );
	$c_b = hexdec( substr( $hex, 4, 2 ) );

	$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

	return $brightness > 155;
}

/**
 * Make HEX color lighter.
 */
function memberhero_hex_lighter( $color, $factor = 30 ) {
	$base  = memberhero_rgb_from_hex( $color );
	$color = '#';

	foreach ( $base as $k => $v ) {
		$amount      = 255 - $v;
		$amount      = $amount / 100;
		$amount      = round( $amount * $factor );
		$new_decimal = $v + $amount;

		$new_hex_component = dechex( $new_decimal );
		if ( strlen( $new_hex_component ) < 2 ) {
			$new_hex_component = '0' . $new_hex_component;
		}
		$color .= $new_hex_component;
	}

	return $color;
}

/**
 * Make HEX color darker.
 */
function memberhero_hex_darker( $color, $factor = 30 ) {
	$base  = memberhero_rgb_from_hex( $color );
	$color = '#';

	foreach ( $base as $k => $v ) {
		$amount      = $v / 100;
		$amount      = round( $amount * $factor );
		$new_decimal = $v - $amount;

		$new_hex_component = dechex( $new_decimal );
		if ( strlen( $new_hex_component ) < 2 ) {
			$new_hex_component = '0' . $new_hex_component;
		}
		$color .= $new_hex_component;
	}

	return $color;
}

/**
 * Convert RGB to HEX.
 */
function memberhero_rgb_from_hex( $color ) {
	$color = str_replace( '#', '', $color );
	// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF".
	$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

	$rgb      = array();
	$rgb['R'] = hexdec( $color{0} . $color{1} );
	$rgb['G'] = hexdec( $color{2} . $color{3} );
	$rgb['B'] = hexdec( $color{4} . $color{5} );

	return $rgb;
}

/**
 * Generate a token.
 */
function memberhero_get_token( $length = 32 ) {
	$token = '';
	$codeAlphabet  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
	$codeAlphabet .= '0123456789';
	for ( $i = 0; $i < $length; $i++ ) {
		$token .= $codeAlphabet[ memberhero_crypto_rand_secure( 0, strlen( $codeAlphabet ) ) ];
	}
	return $token;
}

/**
 * Generate a random string for a token.
 */
function memberhero_crypto_rand_secure( $min, $max ) {
	$range = $max - $min;
	if ( $range < 0 ) {
		return $min;
	}
	$log    = log( $range, 2);
	$bytes  = ( int )( $log / 8 ) + 1;
	$bits   = ( int ) $log + 1;
	$filter = ( int ) ( 1 << $bits ) - 1;
	do {
		$rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
		$rnd = $rnd & $filter;
	} while ( $rnd >= $range );
	return $min + $rnd;
}

/**
 * Limit words in text paragraph.
 */
function memberhero_limit_text( $text, $limit = 20 ) {
	if ( str_word_count( $text, 0 ) > $limit ) {
		$words = str_word_count( $text, 2 );
		$pos   = array_keys( $words );
		$text  = substr( $text, 0, $pos[ $limit ] ) . '...';
	}

	return $text;
}

/**
 * Convert links to clickable links if allowed.
 */
function _memberhero_make_url_clickable_cb( $matches ) {
	$url = $matches[2];

	if ( ')' == $matches[3] && strpos( $url, '(' ) ) {
		// If the trailing character is a closing parethesis, and the URL has an opening parenthesis in it, add the closing parenthesis to the URL.
		// Then we can let the parenthesis balancer do its thing below.
		$url   .= $matches[3];
		$suffix = '';
	} else {
		$suffix = $matches[3];
	}

	// Include parentheses in the URL only if paired
    while ( substr_count( $url, '(' ) < substr_count( $url, ')' ) ) {
		$suffix = strrchr( $url, ')' ) . $suffix;
		$url    = substr( $url, 0, strrpos( $url, ')' ) );
	}

	$url = esc_url( untrailingslashit( $url ) );
	if ( empty( $url ) ) {
		return $matches[0];
	}

	$allowed = apply_filters( 'memberhero_allow_links', true );

	return $allowed ? $matches[1] . "<a href=\"$url\" rel=\"nofollow\" target=\"_blank\">$url</a>" . $suffix : $matches[1] . "[" . __( 'link removed', 'memberhero' ) . "]" . $suffix;
}

/**
 * Convert emails to clickable links if allowed.
 */
function _memberhero_make_email_clickable_cb( $matches ) {
    $email = $matches[2] . '@' . $matches[3];

	$allowed = apply_filters( 'memberhero_allow_emails', true );

    return $allowed ? $matches[1] . "<a href=\"mailto:$email\">$email</a>" : $matches[1] . "[" . __( 'email removed', 'memberhero' ) . "]";
}

/**
 * A function that makes links clickable.
 */
function memberhero_make_clickable( $text ) {
	$r               = '';
	$textarr         = preg_split( '/(<[^<>]+>)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // split out HTML tags
	$nested_code_pre = 0; // Keep track of how many levels link is nested inside <pre> or <code>
	foreach ( $textarr as $piece ) {

		if ( preg_match( '|^<code[\s>]|i', $piece ) || preg_match( '|^<pre[\s>]|i', $piece ) || preg_match( '|^<script[\s>]|i', $piece ) || preg_match( '|^<style[\s>]|i', $piece ) ) {
			$nested_code_pre++;
		} elseif ( $nested_code_pre && ( '</code>' === strtolower( $piece ) || '</pre>' === strtolower( $piece ) || '</script>' === strtolower( $piece ) || '</style>' === strtolower( $piece ) ) ) {
			$nested_code_pre--;
		}

		if ( $nested_code_pre || empty( $piece ) || ( $piece[0] === '<' && ! preg_match( '|^<\s*[\w]{1,20}+://|', $piece ) ) ) {
			$r .= $piece;
			continue;
		}

		// Long strings might contain expensive edge cases ...
		if ( 10000 < strlen( $piece ) ) {
			// ... break it up
			foreach ( _split_str_by_whitespace( $piece, 2100 ) as $chunk ) { // 2100: Extra room for scheme and leading and trailing paretheses
				if ( 2101 < strlen( $chunk ) ) {
					$r .= $chunk; // Too big, no whitespace: bail.
				} else {
					$r .= memberhero_make_clickable( $chunk );
				}
			}
		} else {
			$ret = " $piece "; // Pad with whitespace to simplify the regexes

			$url_clickable = '~
                ([\\s(<.,;:!?])                                        # 1: Leading whitespace, or punctuation
                (                                                      # 2: URL
                    [\\w]{1,20}+://                                # Scheme and hier-part prefix
                    (?=\S{1,2000}\s)                               # Limit to URLs less than about 2000 characters long
                    [\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+         # Non-punctuation URL character
                    (?:                                            # Unroll the Loop: Only allow puctuation URL character if followed by a non-punctuation URL character
                        [\'.,;:!?)]                            # Punctuation URL character
                        [\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]++ # Non-punctuation URL character
                    )*
                )
                (\)?)                                                  # 3: Trailing closing parenthesis (for parethesis balancing post processing)
            ~xS'; // The regex is a non-anchored pattern and does not have a single fixed starting character.
                  // Tell PCRE to spend more time optimizing since, when used on a page load, it will probably be used several times.
 
			$ret = preg_replace_callback( $url_clickable, '_memberhero_make_url_clickable_cb', $ret );
 
			$ret = preg_replace_callback( '#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', '_make_web_ftp_clickable_cb', $ret );
			$ret = preg_replace_callback( '#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_memberhero_make_email_clickable_cb', $ret );
 
			$ret = substr( $ret, 1, -1 ); // Remove our whitespace padding.
			$r  .= $ret;
		}
	}

	// Cleanup of accidental links within links
	return preg_replace( '#(<a([ \r\n\t]+[^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', '$1$3</a>', $r );
}

/**
 * Display a plugin post. Used by 3rd party.
 */
function memberhero_post( $str ) {

	// Make links clickable.
	$str = memberhero_make_clickable( $str );

	return wp_kses_post( $str );
}

/**
 * Get a feed post as used by several plugin components.
 */
function memberhero_get_post_alias( $str ) {

	if ( strstr( memberhero_post( $str ), '<a href=' ) ) {
		$str = __( 'Sent a link', 'memberhero' );
	}

	return wp_kses_post( $str );
}

/**
 * Returns formatted bytes.
 */
function memberhero_format_bytes( $bytes, $precision = 2 ) { 
	$units = array( 'B', 'KB', 'MB', 'GB', 'TB' ); 

	$bytes = max( $bytes, 0 ); 
	$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) ); 
	$pow = min( $pow, count( $units ) - 1 ); 

	$bytes /= pow( 1024, $pow );

	return round( $bytes, $precision ) . ' ' . $units[ $pow ]; 
}

/**
 * Generates a random code or token.
 */
function memberhero_generate_code( $split = 4, $length = 20 ) {
	return implode( '-', str_split( substr( strtolower( md5( microtime() . rand( 1000, 9999 ) ) ), 0, $length ), $split ) );
}

/**
 * Formats an amount.
 */
function memberhero_format_amount( $amount, $decimals = true ) {
	$thousands_sep = get_option( 'memberhero_thousands_separator', ',' );
	$decimal_sep   = get_option( 'memberhero_decimal_separator', '.' );

	// Format the amount
	if ( $decimal_sep == ',' && false !== ( $sep_found = strpos( $amount, $decimal_sep ) ) ) {
		$whole = substr( $amount, 0, $sep_found );
		$part = substr( $amount, $sep_found + 1, ( strlen( $amount ) - 1 ) );
		$amount = $whole . '.' . $part;
	}

	// Strip , from the amount (if set as the thousands separator)
	if ( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( ',', '', $amount );
	}

	// Strip ' ' from the amount (if set as the thousands separator)
	if ( $thousands_sep == ' ' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( ' ', '', $amount );
	}

	if ( empty( $amount ) ) {
		$amount = 0;
	}

	$decimals  = apply_filters( 'memberhero_format_amount_decimals', $decimals ? 2 : 0, $amount );
	$formatted = number_format( $amount, $decimals, $decimal_sep, $thousands_sep );

	return apply_filters( 'memberhero_format_amount', $formatted, $amount, $decimals, $decimal_sep, $thousands_sep );
}