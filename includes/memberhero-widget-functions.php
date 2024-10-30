<?php
/**
 * Member Hero Widget Functions
 *
 * Widget related functions and widget registration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include widget classes.
require_once dirname( __FILE__ ) . '/abstracts/abstract-memberhero-widget.php';
require_once dirname( __FILE__ ) . '/widgets/class-memberhero-widget-user-card.php';

/**
 * Register Widgets.
 */
function memberhero_register_widgets() {
	register_widget( 'MemberHero_Widget_User_Card' );
}
add_action( 'widgets_init', 'memberhero_register_widgets' );

/**
 * User card.
 */
add_action( 'memberhero_user_card', 'memberhero_profile_header', 10 );
add_action( 'memberhero_user_card', 'memberhero_profile_info', 20 );