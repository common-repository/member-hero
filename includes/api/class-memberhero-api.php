<?php
/**
 * API.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_API class.
 */
class MemberHero_API {

	/**
	 * API Version
	 */
	const VERSION = '1.0';

	/**
	 * Pretty Print?
	 */
	private $pretty_print = false;

	/**
	 * Log API requests?
	 */
	public $log_requests = true;

	/**
	 * Is this a valid request?
	 */
	private $is_valid_request = false;

	/**
	 * User ID Performing the API Request
	 */
	private $user_id = 0;

	/**
	 * The permissions for this user.
	 */
	private $permissions = null;

	/**
	 * Response data to return
	 */
	private $data = array();

	/**
	 * Override?
	 */
	private $override = true;

	/**
	 * Setup the Member Hero API
	 */
	public function __construct() {
		add_action( 'init',                     array( $this, 'add_endpoint'     ) );
		add_action( 'template_redirect',        array( $this, 'process_query'    ), -1 );
		add_filter( 'query_vars',               array( $this, 'query_vars'       ) );

		// Determine if JSON_PRETTY_PRINT is available
		$this->pretty_print = defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : null;

		// Allow API request logging to be turned off
		$this->log_requests = apply_filters( 'memberhero_api_log_requests', false );

	}

	/**
	 * Registers a new rewrite endpoint for accessing the API
	 */
	public function add_endpoint() {
		add_rewrite_endpoint( 'memberhero-api', EP_ALL );
	}

	/**
	 * Registers query vars for API access
	 */
	public function query_vars( $vars ) {
		$vars[] = 'key';
		$vars[] = 'secret';
		$vars[] = 'query';
		$vars[] = 'type';
		$vars[] = 'number';
		$vars[] = 'offset';
		$vars[] = 'date';
		$vars[] = 'startdate';
		$vars[] = 'enddate';
		$vars[] = 'format';
		$vars[] = 'id';
		$vars[] = 'data';
		$vars[] = 'email';
		$vars[] = 'login';
		$vars[] = 'search';
		$vars[] = 'orderby';
		$vars[] = 'order';

		return $vars;
	}

	/**
	 * Validate the API request
	 *
	 * Checks for the user's consumer key and secret against the DB data.
	 */
	private function validate_request() {
		global $wp_query;

		$this->override = false;

        // Make sure we have both user and api key
		if ( ! empty( $wp_query->query_vars['memberhero-api'] ) ) {
			if ( empty( $wp_query->query_vars['secret'] ) || empty( $wp_query->query_vars['key'] ) ) {
				$this->missing_auth();
			} else {
				// Let's check the validity of provided keys.
				$key = $wp_query->query_vars['key'];
				$secret = $wp_query->query_vars['secret'];
				if ( ! ( $user = $this->check_user_key( $key, $secret ) ) ) {
					$this->invalid_auth();
				} else {
					// We have a valid request.
					$this->is_valid_request = true;
				}
			}
		}
	}

	/**
	 * Check the user key.
	 */
	private function check_user_key( $consumer_key, $consumer_secret ) {
		global $wpdb;
		$key = $wpdb->get_row( $wpdb->prepare( "SELECT user_id, permissions, key_id FROM {$wpdb->prefix}memberhero_api_keys where consumer_key = %s and consumer_secret = %s LIMIT 1", memberhero_api_hash( $consumer_key ), $consumer_secret ) );
		if ( isset( $key->key_id ) && isset( $key->user_id ) && isset( $key->permissions ) ) {
			$this->set_last_access( $key );
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Set last access for this key.
	 */
	private function set_last_access( $key ) {
		global $wpdb;

		$this->user_id	   = $key->user_id;
		$this->permissions = $key->permissions;

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}memberhero_api_keys SET last_access = '%s' WHERE key_id = %d", current_time( 'mysql', 1 ), $key->key_id ) );
	}

	/**
	 * Listens for the API and then processes the API requests
	 */
	public function process_query() {
		global $wp_query;

		// Check for memberhero-api var. Get out if not present
		if ( ! isset( $wp_query->query_vars['memberhero-api'] ) ) {
			return;
		}

		// Check for a valid user and set errors if necessary
		$this->validate_request();

		// Only proceed if no errors have been noted
		if( ! $this->is_valid_request ) {
			return;
		}

		if( ! defined( 'MEMBERHERO_DOING_API' ) ) {
			define( 'MEMBERHERO_DOING_API', true );
		}

		// Determine the kind of query
		$query_mode = $this->get_query_mode();

		// Get query args.
		foreach( $wp_query->query_vars as $key => $value ) {
			if ( in_array( $key, $this->query_vars( array() ) ) && ! empty( $wp_query->query_vars[ $key ] ) ) {
				$args[ $key ] = $wp_query->query_vars[ $key ];
			}
		}

		$data = array();

		switch( $query_mode ) :
			case 'users.get' :
				$data = $this->get_users( $args );
				break;
			case 'user.get' :
				$data = $this->get_user( $args );
				break;
		endswitch;

		// Allow add-ons to setup their own return data
		$this->data = apply_filters( 'memberhero_api_output_data', $data, $query_mode, $this );

		// Log this API request, if enabled. We log it here because we have access to errors.
		$this->log_request( $this->data );

		// Send out data to the output function
		$this->output();
	}

	/**
	 * Determines the kind of query requested and also ensure it is a valid query
	 */
	public function get_query_mode() {
		global $wp_query;

		// Whitelist our query options
		$accepted = apply_filters( 'memberhero_api_valid_query_modes', array(
			'users.get',
			'user.get',
		) );

		$query = isset( $wp_query->query_vars['memberhero-api'] ) ? $wp_query->query_vars['memberhero-api'] : null;
		$error = array();

		// Make sure our query is valid
		if ( ! in_array( $query, $accepted ) ) {
			$error['error'] = esc_html__( 'Invalid API request.', 'memberhero' );

			$this->data = $error;
			$this->output();
		}

		// Make sure the authenticated user can access this feature.
		if ( $this->user_cannot_request( $query ) ) {
			$error['error'] = esc_html__( 'Not enough permissions.', 'memberhero' );

			$this->data = $error;
			$this->output( 401 );
		}

		return $query;
	}

	/**
	 * Validate the user's key permissions.
	 */
	private function user_cannot_request( $query ) {
		switch( $query ) {
			case 'users.get' :
			if ( $this->permissions == 'read' || $this->permissions == 'write' || $this->permissions == 'read_write' ) {
				return false;
			}
			break;
			case 'user.get' :
			if ( $this->permissions == 'read' || $this->permissions == 'write' || $this->permissions == 'read_write' ) {
				return false;
			}
			break;
		}
		return true;
	}

	/**
	 * Displays a missing authentication error if all the parameters aren't
	 * provided
	 */
	private function missing_auth() {
		$error = array();
		$error['error'] = esc_html__( 'Missing credentials.', 'memberhero' );

		$this->data = $error;
		$this->output( 401 );
	}

	/**
	 * Displays an invalid authentication error.
	 */
	private function invalid_auth() {
		$error = array();
		$error['error'] = esc_html__( 'Invalid credentials.', 'memberhero' );

		$this->data = $error;
		$this->output( 401 );
	}

	/**
	 * Retrieve the output format
	 */
	public function get_output_format() {
		global $wp_query;

		$format = isset( $wp_query->query_vars['format'] ) ? $wp_query->query_vars['format'] : 'json';

		return apply_filters( 'memberhero_api_output_format', $format );
	}

	/**
	 * Output Query in either JSON/XML. The query data is outputted as JSON
	 * by default
	 */
	public function output( $status_code = 200 ) {
		global $wp_query;

		$format = $this->get_output_format();

		status_header( $status_code );

		do_action( 'memberhero_api_output_before', $this->data, $this, $format );

		switch ( $format ) :

			case 'json' :
				header( 'Content-Type: application/json' );
				if ( ! empty( $this->pretty_print ) )
					echo json_encode( $this->data, $this->pretty_print );
				else
					echo json_encode( $this->data );
				break;


			default :
				// Allow other formats to be added via addons
				do_action( 'memberhero_api_output_' . $format, $this->data, $this );
				break;

		endswitch;

		do_action( 'memberhero_api_output_after', $this->data, $this, $format );

		die();
	}

	/**
	 * Log each API request, if enabled
	 */
	private function log_request( $data = array() ) {
		if ( ! $this->log_requests ) {
			return;
		}
	}

	/**
	 * Get users data.
	 */
	public function get_users( $args = array() ) {
		$response = array();

		$fields = array();

		$q = array(
			'fields'	=> 'ID',
			'number'	=> isset( $args[ 'number' ] ) ? $args[ 'number' ] : 10,
			'offset'	=> isset( $args[ 'offset' ] ) ? $args[ 'offset' ] : 0,
			'orderby'	=> isset( $args[ 'orderby' ] ) ? $args[ 'orderby' ] : '',
			'order'		=> isset( $args[ 'order' ] ) ? $args[ 'order' ] : 'desc',
		);

		// Force a maximum amount of results.
		if ( $q['number'] > 50 ) {
			$q['number'] = 50;
		}

		if ( ! empty( $args[ 'data' ] ) ) {
			$fields = explode( ':', $args[ 'data' ] );
		}

		$users = new WP_User_Query( $q );
		if ( $users->get_results() ) {
			foreach( $users->get_results() as $user_id ) {
				$user = memberhero_get_user( $user_id );
				if ( ! empty( $fields ) ) {
					foreach( $fields as $field ) {
						if ( isset( $user->$field ) ) {
							$response['users'][ $user_id ][ $field ] = $user->$field;
						} elseif ( method_exists( $user, 'get_' . $field ) ) {
							$method = 'get_' . $field;
							$response['users'][ $user_id ][ $field ] = $user->$method();
						} else {
							$response['users'][ $user_id ][ $field ] = $user->get( $field );
						}
					}
				} else {
					foreach( $user as $key => $value ) {
						if ( in_array( $key, array( 'user', 'user_id', 'user_activation_key', 'user_status', 'user_pass', 'filter' ) ) ) {
							continue;
						}
						$response['users'][ $user_id ][ $key ] = $value;
					}
				}
			}
		}

		if ( empty( $response ) ) {
			$response['no_records'] = esc_html__( 'No records could be found for this API request.', 'memberhero' );
		}

		return $response;
	}

	/**
	 * Get user data.
	 */
	public function get_user( $args = array() ) {
		$response = array();

		$fields = array();

		// How should we query this user?
		if ( isset( $args['id'] ) ) {
			$user_var = $args['id'];
		} elseif ( isset( $args['email'] ) ) {
			$user_var = $args['email'];
		} elseif ( isset( $args['login'] ) ) {
			$user_var = $args['login'];
		} else {
			$response['missing_user'] = esc_html__( 'Please provide user ID, email, or login.', 'memberhero' );
			return $response;
		}

		if ( ! empty( $args[ 'data' ] ) ) {
			$fields = explode( ':', $args[ 'data' ] );
		}

		$user = memberhero_get_user( $user_var );
		$user_id = $user->ID;

		// Invalid user?
		if ( ! $user_id ) {
			$response['invalid_user'] = esc_html__( 'We cannot find this user.', 'memberhero' );
			return $response;
		}

		if ( ! empty( $fields ) ) {
			foreach( $fields as $field ) {
				if ( isset( $user->$field ) ) {
					$response['user'][ $user_id ][ $field ] = $user->$field;
				} elseif ( method_exists( $user, 'get_' . $field ) ) {
					$method = 'get_' . $field;
					$response['user'][ $user_id ][ $field ] = $user->$method();
				} else {
					$response['user'][ $user_id ][ $field ] = $user->get( $field );
				}
			}
		} else {
			foreach( $user as $key => $value ) {
				if ( in_array( $key, array( 'user', 'user_id', 'user_activation_key', 'user_status', 'user_pass', 'filter' ) ) ) {
					continue;
				}
				$response['user'][ $user_id ][ $key ] = $value;
			}
		}

		if ( empty( $response ) ) {
			$response['no_records'] = esc_html__( 'No records could be found for this API request.', 'memberhero' );
		}

		return $response;
	}

}