<?php
/**
 * License handler.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_License class.
 */
class MemberHero_License {

	/**
	 * Global variables.
	 */
	public $store_url 	= 'https://memberhero.pro/';
	public $option_id  	= '';
	public $version 	= '';
	public $item_id 	= '';
	public $item_name 	= '';
	public $plugin_file = '';

	/**
	 * The Constructor.
	 */
	public function __construct( $option_id = '', $version = '', $item_id = '', $item_name = '', $plugin_file = '' ) {

		$this->option_id 	= $option_id;
		$this->version   	= $version;
		$this->item_id   	= $item_id;
		$this->item_name 	= $item_name;
		$this->plugin_file 	= $plugin_file;

		// Setup hooks
		$this->includes();
		$this->hooks();
	}

	/**
	 * Include the updater class
	 */
	private function includes() {
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) )  {
			require_once 'EDD_SL_Plugin_Updater.php';
		}
	}

	/**
	 * Setup hooks
	 */
	private function hooks() {
		add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );
		add_filter( 'memberhero_license_settings', array( $this, 'add_license' ) );
		add_action( 'memberhero_saved_license_settings', array( $this, 'settings' ) );
	}

	/**
	 * Auto updater
	 */
	public function auto_updater() {

		$license = trim( get_option( $this->option_id ) );

		if ( $license && $this->item_id && $this->item_name && $this->version ) {
			$edd_updater = new EDD_SL_Plugin_Updater( $this->store_url, $this->plugin_file,
				array(
					'version' 	=> $this->version,
					'license' 	=> $license,
					'item_id' 	=> $this->item_id,
					'item_name' => $this->item_name,
					'author'  	=> 'Member Hero',
					'url'     	=> home_url(),
				)
			);
		}
	}

	/**
	 * Deactivate a license.
	 */
	public function deactivate_license() {
		if ( isset( $_GET[ 'deactivate-license' ] ) && isset( $_GET[ 'item' ] ) ) {
			if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'deactivate-license' ) ) ) {
				die( __( 'Security check.', 'memberhero' ) );
			}
			$this->option_id = memberhero_clean( $_GET[ 'deactivate-license' ] );
			$this->item_name = $_GET[ 'item' ];

			$api = $this->_deactivate();

			delete_option( $this->option_id );
			delete_option( $this->option_id . '_expires' );
			delete_option( $this->option_id . '_active' );

			exit( wp_safe_redirect( admin_url( 'admin.php?page=memberhero-settings&tab=licensing' ) ) );
		}
	}

	/**
	 * Add license field.
	 */
	public function add_license( $settings ) {

		$last 	= end( $settings );
		$key 	= key( $settings );

		unset( $settings[ $key ] );

		$settings[] = $this->get_license_field( $this->item_name );
		$settings[] = $last;

		return $settings;
	}

	/**
	 * Get license field.
	 */
	public function get_license_field( $name ) {
		$slug = memberhero_sanitize_title( $name );

		$field = array(
			'title'    => $name,
			'desc'     => sprintf( __( 'To receive updates, please enter your valid %s license key.', 'memberhero' ), $name ),
			'id'       => 'memberhero_' . $slug . '_license',
			'type'     => 'license',
			'item'	   => $name,
		);
		return $field;
	}

	/**
	 * Save settings.
	 */
	public function settings() {
		$key = $this->option_id;

		$new 	= isset( $_POST[ $key ] ) ? memberhero_clean( $_POST[ $key ] ) : '';
		$old 	= get_option( $key );

		// The current key match the saved key. no need to do anything.
		if ( $old && ( $new == $old ) ) {
			return;
		}

		// No need to update anything.
		if ( empty( $old ) && empty( $new ) ) {
			return;
		}

		// User is trying to update license. Deactivate old key.
		if ( $old && ( $new != $old ) ) {
			$deactivate = $this->_deactivate( $old );
			if ( $new ) {
				$result = $this->_activate( $new );
				if ( $result[ 'status' ] != 'valid' ) {
					delete_option( $key );
					delete_option( $key . '_expires' );
					delete_option( $key . '_active' );
				} else {
					update_option( $key, $new );
					update_option( $key . '_expires', $result[ 'expires' ] );
					update_option( $key . '_active', 'yes' );
				}
			} else {
				delete_option( $key );
				delete_option( $key . '_expires' );
				delete_option( $key . '_active' );
			}
		}

		// User entered a license key for first time.
		if ( empty( $old ) && $new ) {
			$result = $this->_activate( $new );
			if ( $result[ 'status' ] != 'valid' ) {
				delete_option( $key );
				delete_option( $key . '_expires' );
				delete_option( $key . '_active' );
			} else {
				update_option( $key, $new );
				update_option( $key . '_expires', $result[ 'expires' ] );
				update_option( $key . '_active', 'yes' );
			}
		}
	}

	/**
	 * Activate a license.
	 */
	public function _activate( $code ) {

		$license = empty( $code ) ? trim( get_option( $this->option_id ) ) : trim( $code );

		$api_params = array(
			'edd_action' 	=> 'activate_license',
			'license' 		=> $license,
			'item_name' 	=> urlencode( $this->item_name ),
			'url'       	=> home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'memberhero' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :
						$message = sprintf(
							__( 'Your license key expired on %s.', 'memberhero' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
					break;

					case 'disabled' :
					case 'revoked' :
						$message = __( 'Your license key has been disabled.', 'memberhero' );
					break;

					case 'missing' :
						$message = __( 'Invalid license.', 'memberhero' );
					break;

					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.', 'memberhero' );
					break;

					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'memberhero' ), $this->item_name );
					break;

					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'memberhero' );
					break;

					default :
						$message = __( 'An error occurred, please try again.', 'memberhero' );
					break;
				}
			}
		}

		// Return activation response as array.
		$result = array(
			'status'		=> $license_data->license,
			'message'		=> ! empty( $message ) ? $message : null,
			'expires'		=> ! empty( $license_data->expires ) ? $license_data->expires : null,
		);

		return $result;
	}

	/**
	 * Deactivate a license.
	 */
	public function _deactivate( $code = null ) {
		// retrieve the license from the database
		$license = empty( $code ) ? trim( get_option( $this->option_id ) ) : trim( $code );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'memberhero' );
			}

		}

		// $license_data->license will be either "deactivated" or "failed"
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// Return activation response as array.
		$result = array(
			'status'	=> $license_data->license,
			'message'	=> ! empty( $message ) ? $message : null
		);

		return $result;
	}

}