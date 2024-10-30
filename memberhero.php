<?php
/**
 * Plugin Name: Member Hero
 * Plugin URI: https://memberhero.pro
 * Description: Build your own membership site without code. Customize signups, logins, profiles, directories, content restriction, member roles and more.
 * Author: Member Hero
 * Author URI: https://memberhero.pro/
 * Version: 1.0.9
 * Text Domain: memberhero
 * Domain Path: /i18n/languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define MEMBERHERO_PLUGIN_FILE.
if ( ! defined( 'MEMBERHERO_PLUGIN_FILE' ) ) {
	define( 'MEMBERHERO_PLUGIN_FILE', __FILE__ );
}

// Define MEMBERHERO_PLUGIN_BASENAME.
if ( ! defined( 'MEMBERHERO_PLUGIN_BASENAME' ) ) {
	define( 'MEMBERHERO_PLUGIN_BASENAME', plugin_basename( MEMBERHERO_PLUGIN_FILE ) );
}

// Include the main class.
if ( ! class_exists( 'MemberHero' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-memberhero.php';
}

/**
 * Main instance.
 */
function memberhero() {
	return MemberHero::instance();
}

// Global for backwards compatibility.
$GLOBALS[ 'memberhero' ] = memberhero();