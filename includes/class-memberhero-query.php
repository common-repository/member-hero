<?php
/**
 * Contains the query functions which alter the front-end post queries and loops
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Query class.
 */
class MemberHero_Query {

	/**
	 * Query vars to add to wp.
	 */
	public $query_vars = array();

	/**
	 * Constructor for the query class. Hooks in methods.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
		if ( ! is_admin() ) {
			add_action( 'wp_loaded', array( $this, 'get_errors' ), 20 );
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
			add_action( 'parse_request', array( $this, 'parse_request' ), 0 );
		}
		$this->init_query_vars();
	}

	/**
	 * Get any errors from querystring.
	 */
	public function get_errors() {
		$error = ! empty( $_GET['memberhero_error'] ) ? sanitize_text_field( wp_unslash( $_GET['memberhero_error'] ) ) : '';

		if ( $error && ! memberhero_has_notice( $error, 'error' ) ) {
			memberhero_add_notice( $error, 'error' );
		}
	}

	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {
		// Query vars to add to WP.
		$this->query_vars = array_merge( $this->query_vars, memberhero_get_account_endpoints(), memberhero_get_profile_endpoints() );
	}

	/**
	 * Get page title for an endpoint.
	 */
	public function get_endpoint_title( $endpoint ) {
		global $wp;

		switch ( $endpoint ) {
			case 'edit-account' :
				$title = __( 'Account', 'memberhero' );
				break;
			case 'edit-password' :
				$title = __( 'Password', 'memberhero' );
				break;
			case 'privacy' :
				$title = __( 'Privacy', 'memberhero' );
				break;
			case 'email-notifications' :
				$title = __( 'Email notifications', 'memberhero' );
				break;
			case 'blocked' :
				$title = __( 'Blocked accounts', 'memberhero' );
				break;
			case 'logout' :
				$title = __( 'Logout', 'memberhero' );
				break;
			case 'delete' :
				$title = __( 'Delete account', 'memberhero' );
				break;
			default:
				$title = '';
				break;
		}

		return apply_filters( 'memberhero_endpoint_' . $endpoint . '_title', $title, $endpoint );
	}

	/**
	 * Get page description for an endpoint.
	 */
	public function get_endpoint_desc( $endpoint ) {
		global $wp;

		switch ( $endpoint ) {
			case 'edit-password':
				$title = __( 'Change your password or recover your current one.', 'memberhero' );
				break;
			default:
				$title = '';
				break;
		}

		return apply_filters( 'memberhero_endpoint_' . $endpoint . '_desc', $title, $endpoint );
	}

	/**
	 * Endpoint mask describing the places the endpoint should be added.
	 */
	public function get_endpoints_mask() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_on_front     = get_option( 'page_on_front' );
			$myaccount_page_id = get_option( 'memberhero_account_page_id' );

			if ( in_array( $page_on_front, array( $myaccount_page_id ), true ) ) {
				return EP_ROOT | EP_PAGES;
			}
		}

		return EP_PAGES;
	}

	/**
	 * Add endpoints for query vars.
	 */
	public function add_endpoints() {
		$mask = $this->get_endpoints_mask();

		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( $key == 'memberhero_user' ) {

				$profile_page_id 	= get_option( 'memberhero_profile_page_id' );
				$profile			= get_post( $profile_page_id );

				if ( isset( $profile->post_name ) ) {
					add_rewrite_tag( '%memberhero_user%', '([^/]+)' );
					add_rewrite_tag( '%memberhero_tab%', '([^/]+)' );
					add_rewrite_rule( $profile->post_name . '/([^/]+)/?$', 'index.php?pagename=' . $profile->post_name . '&memberhero_user=$matches[1]', 'top' );
					add_rewrite_rule( $profile->post_name . '/([^/]+)/([^/]+)/?$', 'index.php?pagename=' . $profile->post_name . '&memberhero_user=$matches[1]&memberhero_tab=$matches[2]', 'top' );
				}

			} elseif ( ! empty( $var ) ) {
				add_rewrite_endpoint( $var, $mask );
			}
		}
	}

	/**
	 * Add query vars.
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->get_query_vars() as $key => $var ) {
			$vars[] = $key;
		}
		return $vars;
	}

	/**
	 * Get query vars.
	 */
	public function get_query_vars() {
		return apply_filters( 'memberhero_get_query_vars', $this->query_vars );
	}

	/**
	 * Get query current active query var.
	 */
	public function get_current_endpoint() {
		global $wp;

		foreach ( $this->get_query_vars() as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return $key;
			}
		}
		return '';
	}

	/**
	 * Parse the request and look for query vars - endpoints may not be supported.
	 */
	public function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported.
		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $var ] ) );
			} elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}

	/**
	 * Are we currently on the front page?
	 */
	private function is_showing_page_on_front( $q ) {
		return $q->is_home() && 'page' === get_option( 'show_on_front' );
	}

	/**
	 * Is the front page a page we define?
	 */
	private function page_on_front_is( $page_id ) {
		return absint( get_option( 'page_on_front' ) ) === absint( $page_id );
	}

}