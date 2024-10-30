<?php
/**
 * Locale Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Hooks.
add_filter( 'gettext', 'memberhero_gettext', 200, 3 );

/**
 * Get all translations or locale specific translations.
 */
function memberhero_get_translations( $locale = '' ) {
	if ( ! empty( $locale ) ) {
		$locale = memberhero_sanitize_title( $locale );
		return isset( memberhero()->translations[ $locale ] ) ? memberhero()->translations[ $locale ] : null;
	}
	return memberhero()->translations;
}

/**
 * Add a translation.
 */
function memberhero_add_translation( $text = null, $translated_text = null, $locale = 'en_US' ) {
	if ( empty( $text ) || empty( $translated_text ) ) {
		return;
	}

	$locale	 = memberhero_sanitize_title( $locale );
	$text	 = esc_attr( $text );

	// already added.
	if ( isset( memberhero()->translations[ $locale ][ $text ] ) ) {
		return;
	}

	memberhero()->translations[ $locale ][ $text ] = $translated_text;
	update_option( 'memberhero_translations', memberhero()->translations );
}

/**
 * Add bulk translations.
 */
function memberhero_add_bulk_translation( $words = array(), $locale = 'en_US' ) {
	if ( empty( $words ) ) {
		return;
	}

	$locale = memberhero_sanitize_title( $locale );

	foreach( $words as $untranslated => $translated ) {
		$untranslated = esc_attr( $untranslated );
		memberhero()->translations[ $locale ][ $untranslated ] = $translated;
	}

	update_option( 'memberhero_translations', memberhero()->translations );
}

/**
 * Update translation.
 */
function memberhero_update_translation( $text = null, $translated_text = null, $locale = 'en_US' ) {
	if ( empty( $text ) || empty( $translated_text ) ) {
		return;
	}

	$locale	 = memberhero_sanitize_title( $locale );
	$text	 = esc_attr( $text );

	memberhero()->translations[ $locale ][ $text ] = $translated_text;
	update_option( 'memberhero_translations', memberhero()->translations );
}

/**
 * Remove a translation.
 */
function memberhero_remove_translation( $text = null, $locale = 'en_US' ) {
	if ( empty( $text ) ) {
		return;
	}

	$locale       = memberhero_sanitize_title( $locale );
	$text	      = esc_attr( $text );

	// remove from translations.
	if ( isset( memberhero()->translations[ $locale ][ $text ] ) ) {
		unset( memberhero()->translations[ $locale ][ $text ] );
	}

	update_option( 'memberhero_translations', memberhero()->translations );
}

/**
 * Clear all translations.
 */
function memberhero_clear_translations() {
	delete_option( 'memberhero_translations' );
}

/**
 * Filter translated words.
 */
function memberhero_gettext( $translated_text, $text, $domain ) {
	// This is our plugin textdomain.
	if ( $domain !== 'memberhero' ) {
		return $translated_text;
	}

	$locale       = memberhero_sanitize_title( get_locale() );
	$text 		  = esc_attr( $text );

	// Get custom translation if found for this locale.
	if ( isset( memberhero()->translations[ $locale ][ $text ] ) ) {
		return esc_html( memberhero()->translations[ $locale ][ $text ] );
	}

	return $translated_text;
}