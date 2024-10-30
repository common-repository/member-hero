<?php
/**
 * Profile Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get profile endpoints.
 */
function memberhero_get_profile_endpoints() {
	$endpoints = array(
		'memberhero_user'	=> '',
	);

	$endpoints = apply_filters( 'memberhero_get_profile_endpoints', ( array ) $endpoints );

	return $endpoints;
}

/**
 * Get active profile ID.
 */
function memberhero_get_active_profile_id() {
	$user_id = username_exists( esc_attr( get_query_var( 'memberhero_user' ) ) );

	// Get the profile ID when form is being sent via AJAX.
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$user_id = absint( $_REQUEST[ '_user_id' ] );
	}

	return apply_filters( 'memberhero_get_active_profile_id', $user_id );
}

/**
 * Handle the profile view and possible redirect.
 */
function memberhero_handle_profile_view() {
	global $current_user;

	$user      = esc_attr( get_query_var( 'memberhero_user' ) );
	$user_id   = username_exists( $user );
	$logged_in = is_user_logged_in() ? 1 : 0;

	switch( $logged_in ) {
		case 1 :
			if ( empty( $user ) ) {
				wp_safe_redirect( memberhero_get_profile_url( $current_user->user_login ) );
				exit;
			}
			if ( ! $user_id ) {
				wp_safe_redirect( add_query_arg( array( 'user_not_found' => 'true' ), home_url() ) );
				exit;
			}
			if ( is_memberhero_inactive_user( $user_id ) ) {
				if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_memberhero' ) && ! current_user_can( 'memberhero_mod_users' ) ) {
					wp_safe_redirect( add_query_arg( array( 'inactive' => 'true' ), home_url() ) );
					exit;
				}
			}
			if ( ! current_user_can( 'memberhero_view_profiles' ) && $user_id != get_current_user_id() ) {
				wp_safe_redirect( add_query_arg( 'unauthorized', 'true', home_url() ) );
				exit;
			}
		break;
		case 0 :
			if ( empty( $user ) || ! $user_id ) {
				wp_safe_redirect( add_query_arg( array( 'user_not_found' => 'true' ), home_url() ) );
				exit;
			}
			if ( is_memberhero_inactive_user( $user_id ) ) {
				wp_safe_redirect( add_query_arg( array( 'inactive' => 'true' ), home_url() ) );
				exit;
			}
			if ( get_option( 'memberhero_disable_public_profiles' ) == 'yes' && ! isset( $_GET[ 'login' ] ) ) {
				wp_safe_redirect( add_query_arg( array( 'login' => 'true' ) ) );
				exit;
			}
		break;
	}

	$tab = get_query_var( 'memberhero_tab' );

	// Strip the view endpoint.
	if ( $tab === 'view' ) {
		wp_safe_redirect( memberhero_get_profile_url( $user ) );
		exit;
	}

	// Can not edit.
	if ( $tab === 'edit' && ! memberhero_user_can_edit_profile( $user_id ) ) {
		wp_safe_redirect( memberhero_get_profile_url( $user ) );
		exit;
	}

	// Let's check for disabled endpoints.
	if ( 'no' === get_option( 'memberhero_' . $tab . '_tab' ) ) {
		wp_safe_redirect( memberhero_get_profile_url( $user ) );
		exit;
	}

	// User may be trying to access unauthorized tab. That does not belong to them.
	if ( get_current_user_id() != $user_id && ( in_array( memberhero_get_profile_endpoint(), apply_filters( 'memberhero_inacessible_profile_endpoints', array() ) ) ) ) {
		wp_safe_redirect( memberhero_get_profile_url( $user ) );
		exit;
	}
}

/**
 * Get a user profile URL.
 */
function memberhero_get_profile_url( $user = '', $ajax = false ) {
	global $the_user;

	// When profile is not setup, fallback to author posts url.
	if ( memberhero_get_page_id( 'profile' ) <= 0 ) {
		return apply_filters( 'memberhero_get_profile_url_fallback', get_author_posts_url( is_numeric( $user ) ? $user : username_exists( $user ) ) );
	}

	// If empty, assume we are in global.
	if ( empty( $user ) ) {
		$user = $the_user->get( 'user_login' );
	}

	// Get user login by ID.
	if ( is_numeric( $user ) ) {
		$check_user_id = get_userdata( $user );
		if ( $check_user_id ) {
			$the_user 	= memberhero_get_user( $user );
			$user 		= $the_user->user_login;
		}
	}

	$permalink = memberhero_get_page_permalink( 'profile' );

	if ( $ajax ) {
		$user = '<span class="memberhero-ajax">' . $user . '</span>';
	}

	return apply_filters( 'memberhero_get_profile_url', memberhero_get_endpoint_url( $user, '', $permalink ), $user, $the_user );
}

/**
 * Get the current logged in user profile URL.
 */
function memberhero_get_current_user_profile() {
	global $logged_user;

	if ( ! is_user_logged_in() ) {
		return;
	}

	// Get logged user object if it's not yet in memory.
	if ( ! isset( $logged_user->ID ) ) {
		$logged_user = memberhero_get_user( get_current_user_id() );
	}

	$user 	   = $logged_user->get( 'user_login' );
	$permalink = memberhero_get_page_permalink( 'profile' );

	return apply_filters( 'memberhero_get_current_user_profile', memberhero_get_endpoint_url( $user, '', $permalink ), $user, $logged_user );
}

/**
 * Get an endpoint for the current logged in user.
 */
function memberhero_get_current_user_endpoint( $endpoint ) {
	// Ignore the default 'view'
	if ( $endpoint == 'view' ) {
		$endpoint = null;
	}

	return memberhero_get_endpoint_url( $endpoint, '', memberhero_get_current_user_profile() );	
}

/**
 * Get profile tabs.
 */
function memberhero_get_profile_menu() {
	global $the_form;

	$array = array(
		'view'			=> array(
			'label'		=> __( 'Profile', 'memberhero' ),
			'icon'		=> 'memberhero_get_small_avatar',
		),
	);

	if ( ! empty( $the_form ) ) {
		if ( $the_form->show_members_menu !== 'no' ) {
			$array[ 'members' ] = array(
				'label'		=> __( 'Members', 'memberhero' ),
				'icon'		=> 'user',
				'url'		=> memberhero_get_page_permalink( 'list' ),
			);
		}
	}

	// When user is not logged in.
	if ( ! is_user_logged_in() ) {
		$array = array(
			'register'	=> array(
				'label'		=> __( 'Register', 'memberhero' ),
				'icon'		=> 'user-plus',
				'url'		=> memberhero_get_page_permalink( 'register' )
			),
			'login'		=> array(
				'label'		=> __( 'Login', 'memberhero' ),
				'icon'		=> 'log-in',
				'url'		=> memberhero_get_page_permalink( 'login' )
			),
		);

		return apply_filters( 'memberhero_get_profile_menu_noauth', $array );
	}

	return apply_filters( 'memberhero_get_profile_menu', $array );
}

/**
 * Add user avatar to profile menu.
 */
function memberhero_get_small_avatar() {
	return get_avatar( get_current_user_id(), 48 );
}

/**
 * Adds account menu item to profile menu.
 */
add_filter( 'memberhero_get_profile_menu', 'memberhero_add_account_settings_link', 800 );
function memberhero_add_account_settings_link( $array ) {
	$array[ 'account' ] = array(
		'label'		=> __( 'Account &amp; Settings', 'memberhero' ),
		'icon'		=> 'settings',
		'url'		=> memberhero_get_page_permalink( 'account' )
	);

	return $array;
}

/**
 * Adds logout menu item to profile menu.
 */
add_filter( 'memberhero_get_profile_menu', 'memberhero_add_logout_link', 9999 );
function memberhero_add_logout_link( $array ) {
	$array[ 'logout' ] = array(
		'label'		=> __( 'Log out', 'memberhero' ),
		'icon'		=> 'log-out',
		'url'		=> memberhero_logout_url( memberhero_get_current_url() ),
	);

	return $array;
}

/**
 * Display profile tab items.
 */
function memberhero_get_profile_tabs() {
	$array = array(
		'view'			=> array(
			'label'		=> __( 'About', 'memberhero' ),
		),
		'posts'			=> array(
			'label'		=> __( 'Posts', 'memberhero' ),
			'count'		=> true,
			'count_cb'  => 'memberhero_get_user_posts_count',
		),
		'comments'		=> array(
			'label'		=> __( 'Comments', 'memberhero' ),
			'count'		=> true,
			'count_cb'  => 'memberhero_get_user_comments_count',
		),
	);

	// Unset 'posts' tab if its turned off.
	if ( 'no' === get_option( 'memberhero_posts_tab' ) ) {
		unset( $array[ 'posts' ] );
	}

	// Unset 'comments' tab if its turned off.
	if ( 'no' === get_option( 'memberhero_comments_tab' ) ) {
		unset( $array[ 'comments' ] );
	}

	return apply_filters( 'memberhero_get_profile_tabs', $array );
}

/**
 * Get profile default endpoint.
 */
function memberhero_get_profile_default_endpoint() {
	return apply_filters( 'memberhero_get_profile_default_endpoint', 'view' );
}

/**
 * Get profile endpoint URL.
 */
function memberhero_get_profile_endpoint_url( $endpoint, $user = null ) {
	// Ignore the default 'view'
	if ( $endpoint == 'view' ) {
		$endpoint = null;
	}

	return memberhero_get_endpoint_url( $endpoint, '', memberhero_get_profile_url( $user ) );
}

/**
 * Gets the link to the users edit profile page.
 */
function memberhero_get_edit_user_link( $user_id = 0 ) {
	$link = memberhero_get_page_id( 'profile' ) > 0 ? memberhero_get_endpoint_url( 'edit', '', memberhero_get_profile_url( $user_id ) ) : get_edit_user_link( $user_id );
	return apply_filters( 'memberhero_get_edit_user_link', $link );
}

/**
 * Get profile endpoint form.
 */
function memberhero_get_profile_endpoint_form() {
	$endpoint = get_query_var( 'memberhero_tab' );

	if ( $endpoint && ! in_array( $endpoint, array( 'edit', 'view' ) ) ) {
		return get_option( 'memberhero_profile_' . memberhero_sanitize_title( $endpoint ) . '_form' );
	}

	return apply_filters( 'memberhero_profile_form_id', get_option( 'memberhero_profile_form', '' ) );
}

/**
 * This loads a different form per user role.
 */
function memberhero_profile_form_id( $form_id = '' ) {
	global $the_user;
	$form = ! empty( $the_user->ID ) ? absint( get_option( 'memberhero_profile_form_' . esc_attr( $the_user->get_role() ) ) ) : '';

	$updated_id = $form > 0 ? $form : $form_id;

	if ( $updated_id == $form_id ) {
		$is_forced = get_post_meta( $form_id, 'force_role', true );
		$has_role = get_post_meta( $form_id, 'role', true );
		
		if ( $is_forced == 'yes' && $has_role != $the_user->get_role() ) {
			return 0;
		}
	}

	return $updated_id;
}
add_filter( 'memberhero_profile_form_id', 'memberhero_profile_form_id', 1 );

/**
 * Get profile menu items.
 */
function memberhero_get_profile_menu_items( $in_tabs = false, $menu_context = null ) {
	$endpoints = $in_tabs ? memberhero_get_profile_tabs() : memberhero_get_profile_menu();

	if ( $menu_context ) {
		$endpoints = $menu_context;
	}

	// Fail if endpoints are empty.
	if ( empty( $endpoints ) ) {
		return;
	}

	$default = memberhero_get_profile_default_endpoint();

	// Remove missing endpoints.
	foreach ( $endpoints as $endpoint_id => $endpoint ) {
		if ( empty( $endpoint ) ) {
			unset( $items[ $endpoint_id ] );
		}
		$items[ $endpoint_id ] = $endpoint;
	}

	// Make sure that default endpoint comes on top.
	if ( isset( $items[ $default ] ) ) {
		$default_endpoint = $items[ $default ];
		unset( $items[ $default ] );
		$items = array_merge( array( $default => $default_endpoint ), $items );
	}

	return apply_filters( 'memberhero_get_profile_menu_items', $items, $endpoints );
}

/**
 * Get profile menu items classes.
 */
function memberhero_get_profile_menu_item_classes( $endpoint, $in_tabs = false ) {
	global $wp, $the_user, $logged_user;

	$classes = array(
		'memberhero-profile-navigation-link',
		'memberhero-profile-navigation-link--' . $endpoint,
	);

	$current = get_query_var( 'memberhero_tab' ) != '' && get_query_var( 'memberhero_tab' ) == $endpoint ? true : false;

	// Default tab.
	if ( ! get_query_var( 'memberhero_tab' ) && ( $endpoint === memberhero_get_profile_default_endpoint() ) ) {
		if ( is_memberhero_profile_page() ) {
			$current = true;
		}
	}

	if ( is_memberhero_account_page() && $endpoint == 'account' ) {
		$current = true;
	}

	// When we are editing a profile.
	if ( get_query_var( 'memberhero_tab' ) == 'edit' && $endpoint == 'view' ) {
		$current[] = 'is-active';
	}

	// Highlight the correct menu item.
	if ( $current ) {
		if ( ! $in_tabs && get_current_user_id() === $the_user->ID ) {
			$classes[] = 'is-active';
		} else if ( $in_tabs ) {
			$classes[] = 'is-active';
		}
	}

	$classes = apply_filters( 'memberhero_profile_menu_item_classes', $classes, $endpoint );

	return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
}

/**
 * Display the icon related to an item in profile menu.
 */
function memberhero_profile_menu_icon( $data ) {
	if ( ! empty( $data['icon'] ) ) {
		if ( strstr( $data['icon'], 'memberhero_' ) ) {
			if ( function_exists( $data['icon'] ) ) {
				echo call_user_func( $data['icon'] );
			}
		} else {
			echo memberhero_svg_icon( $data['icon'] );
		}
	}
}

/**
 * Display the count for a menu item.
 */
function memberhero_profile_menu_count( $data ) {
	if ( isset( $data['count'], $data['count_cb'] ) && function_exists( $data['count_cb'] ) ) {
	?>
	<span class="memberhero-nav-num count count-<?php echo absint( call_user_func( $data['count_cb'] ) ); ?>">
			<?php echo call_user_func( $data['count_cb'] ); ?>
	</span>
	<?php
	}
}

/**
 * Get profile classes.
 */
function memberhero_get_profile_classes( $classes = array() ) {
	global $the_form;

	$keys = array_keys( memberhero_get_profile_tabs() );

	if ( ! in_array( memberhero_get_profile_endpoint(), $keys ) && memberhero_get_profile_endpoint() != 'edit' ) {
		$classes[] = 'memberhero-profile-noheader';
	}

	$classes[] = 'memberhero-profile-' . memberhero_get_profile_endpoint();

	if ( $the_form->show_menu === 'no' ) {
		$classes[] = 'no-menu';
	}

	if ( $the_form->show_cover === 'no' ) {
		$classes[] = 'no-cover';
	}

	if ( $the_form->aligncenter === 'yes' ) {
		$classes[] = 'is-center';
	}

	return apply_filters( 'memberhero_get_profile_classes', array_unique( $classes ) );
}

/**
 * Get the current profile endpoint.
 */
function memberhero_get_profile_endpoint() {
	$endpoint = get_query_var( 'memberhero_tab' );

	if ( ! $endpoint ) {
		$endpoint = 'view';
	}

	return apply_filters( 'memberhero_get_profile_endpoint', $endpoint );
}

/**
 * Get social links for a user.
 */
function memberhero_get_social_links( $user_id = 0 ) {
	$links = array();

	// Supported networks.
	$sites = apply_filters( 'memberhero_get_social_links_networks', array(
		'facebook',
		'twitter',
		'instagram'
	) );

	foreach( $sites as $site ) {
		$data = get_user_meta( $user_id, '_memberhero_' . $site, true );
		if ( memberhero_clean( $data ) ) {
			$icon = memberhero_get_social_icon_html( $site );
			$links[] = '<a href="' . esc_url( $data ) . '" class="memberhero-icon-badge memberhero-icon-' . $site . '" rel="nofollow" target="_blank">' . $icon . '</a>';
		}
	}

	return $links;
}

/**
 * Get a social icon for a specific network.
 */
function memberhero_get_social_icon_html( $site ) {
	$html = null;

	switch( $site ) {
		// Facebook
		case 'facebook' :
			$html = '<svg aria-hidden="true" viewBox="0 0 18 18"><path d="M1.88 1C1.4 1 1 1.4 1 1.88v14.24c0 .48.4.88.88.88h7.67v-6.2H7.46V8.4h2.09V6.61c0-2.07 1.26-3.2 3.1-3.2.88 0 1.64.07 1.87.1v2.16h-1.29c-1 0-1.19.48-1.19 1.18V8.4h2.39l-.31 2.42h-2.08V17h4.08c.48 0 .88-.4.88-.88V1.88c0-.48-.4-.88-.88-.88H1.88z"></path></svg>';
		break;
		// Twitter
		case 'twitter' :
			$html = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>';
		break;
		// Instagram
		case 'instagram' :
			$html = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"><path d="M 16 3 C 8.83 3 3 8.83 3 16 L 3 34 C 3 41.17 8.83 47 16 47 L 34 47 C 41.17 47 47 41.17 47 34 L 47 16 C 47 8.83 41.17 3 34 3 L 16 3 z M 37 11 C 38.1 11 39 11.9 39 13 C 39 14.1 38.1 15 37 15 C 35.9 15 35 14.1 35 13 C 35 11.9 35.9 11 37 11 z M 25 14 C 31.07 14 36 18.93 36 25 C 36 31.07 31.07 36 25 36 C 18.93 36 14 31.07 14 25 C 14 18.93 18.93 14 25 14 z M 25 16 C 20.04 16 16 20.04 16 25 C 16 29.96 20.04 34 25 34 C 29.96 34 34 29.96 34 25 C 34 20.04 29.96 16 25 16 z"></path></svg>';
		break;
	}

	return apply_filters( 'memberhero_get_social_icon_html', $html, $site );
}