<?php
/**
 * Member Hero Admin Import Users.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Admin_Import_Users class.
 */
class MemberHero_Admin_Import_Users {

	/**
	 * Initialize admin actions.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'actions' ) );
	}

	/**
	 * admin actions.
	 */
	public function actions() {
		if ( $this->is_import_users_settings_page() ) {
			if ( isset( $_POST[ 'import_users' ] ) ) {
				$this->import_users();
			}
		}
	}

	/**
	 * Check if is API Keys settings page.
	 */
	private function is_import_users_settings_page() {
		return isset( $_GET['page'], $_GET['tab'], $_GET['section'] ) && 'memberhero-settings' === $_GET['page'] && 'general' === $_GET['tab'] && 'import' === $_GET['section']; // WPCS: input var okay, CSRF ok.
	}

	/**
	 * Page output.
	 */
	public static function page_output() {
		// Hide the save button.
		$GLOBALS['hide_save_button'] = true;

		include dirname( __FILE__ ) . '/settings/views/html-import-users.php';
	}

	/**
	 * Notices.
	 */
	public static function notices() {

	}

	/**
	 * Import users.
	 */
	public function import_users() {
		$count = 0;

		check_admin_referer( 'memberhero-settings' );

		if ( ! current_user_can( 'manage_memberhero' ) ) {
			wp_die( __( 'You do not have permission to import users.', 'memberhero' ) );
		}

		$overrides = isset( $_POST[ 'import_override' ] ) ? true : false;
		$notify    = isset( $_POST[ 'import_notification'] ) ? true : false;

		// Invalid csv.
		$is_csv = isset( $_FILES[ 'csv_file' ] ) && ! empty( $_FILES[ 'csv_file' ][ 'size' ] );
		if ( ! $is_csv ) {
			MemberHero_Admin_Settings::add_error( __( 'Please upload a valid CSV file.', 'memberhero' ) );
		} else {

			// Get users data from csv.
			$users = $this->csv_to_array( $_FILES[ 'csv_file' ][ 'tmp_name' ] );

			// We run this loop only if we have an array of data.
			if ( is_array( $users ) && ! empty( $users ) ) {
				foreach( $users as $key => $data ) {

					$data[ 'user_login' ] = ! empty( $data[ 'user_login' ] ) ? $data[ 'user_login' ] : null;
					$data[ 'user_email' ] = ! empty( $data[ 'user_email' ] ) ? $data[ 'user_email' ] : null;
					$data[ 'user_pass' ]  = ! empty( $data[ 'user_pass' ] )  ? $data[ 'user_pass' ]  : wp_generate_password( 12, false );

					// Flag to update existing users is checked.
					if ( $overrides ) {
						$user_id = username_exists( $data[ 'user_login' ] );
						if ( ! $user_id ) {
							$user_id = email_exists( $data[ 'user_email' ] );
						}
						// User already exists we should update.
						if ( $user_id ) {
							wp_update_user( array_merge( array( 'ID' => $user_id ), $data ) );
							memberhero_update_usermeta( $user_id, $data );
							$count++;
						} else {
							// We try to insert the user from data.
							$user_id = wp_insert_user( $data );
							if ( ! is_wp_error( $user_id ) ) {
								memberhero_update_usermeta( $user_id, $data );
								if ( $notify ) {
									memberhero_new_user_notification( $user_id, $data[ 'user_pass' ] );
								}
								$count++;
							}
						}

					} else {

						$user_id = wp_insert_user( $data );
						if ( ! is_wp_error( $user_id ) ) {
							memberhero_update_usermeta( $user_id, $data );
							if ( $notify ) {
								memberhero_new_user_notification( $user_id, $data[ 'user_pass' ] );
							}
							$count++;
						}

					}
				}

				MemberHero_Admin_Settings::add_message( sprintf( _n( '%d user successfully imported.', '%d users successfully imported.', $count, 'memberhero' ), $count ) );

			} else {

				MemberHero_Admin_Settings::add_error( __( 'Invalid CSV file format.', 'memberhero' ) );
			}
		}

	}

	/**
	 * Generates array from csv file.
	 */
	private function csv_to_array( $file ) {
		$array 	= null;

		$file 	= $_FILES[ 'csv_file' ][ 'tmp_name' ];
		$rows   = array_map( 'str_getcsv', file( $file ) );
		$header = array_shift( $rows );
		$header = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $header );
		$array  = array();

		foreach( $rows as $row ) {
			$array[] = array_combine( $header, $row );
		}

		return $array;
	}

}

new MemberHero_Admin_Import_Users();