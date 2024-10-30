<?php
/**
 * MemberHero Uninstall
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

define( 'MEMBERHERO_ABSPATH', dirname( __FILE__ ) . '/' );
define( 'MEMBERHERO_PLUGIN_FILE', __FILE__ );
define( 'MEMBERHERO_PLUGIN_BASENAME', plugin_basename( MEMBERHERO_PLUGIN_FILE ) );

// Load the install class into memory to uninstall.
include_once dirname( __FILE__ ) . '/includes/class-memberhero-install.php';
MemberHero_Install::uninstall();