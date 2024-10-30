<?php
/**
 * Advanced Settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'MemberHero_Settings_Advanced', false ) ) {
	return new MemberHero_Settings_Advanced();
}

/**
 * MemberHero_Settings_Advanced class.
 */
class MemberHero_Settings_Advanced extends MemberHero_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'advanced';
		$this->label = __( 'Advanced', 'memberhero' );

		add_filter( 'memberhero_account_page_settings', array( $this, 'account_endpoint_setting' ) );
		parent::__construct();
		$this->notices();
	}

	/**
	 * Get sections.
	 */
	public function get_sections() {
		$sections = array(
			''             	=> __( 'Page setup', 'memberhero' ),
			'account'	   	=> __( 'Account', 'memberhero' ),
			'profile'	  	=> __( 'Profile', 'memberhero' ),
			'keys'          => __( 'REST API', 'memberhero' ),
		);

		return apply_filters( 'memberhero_get_sections_' . $this->id, $sections );
	}

	/**
	 * Output a color picker input box.
	 */
	public function color_picker( $name, $id, $value, $desc = '' ) {
		echo '<div class="color_box">' . memberhero_help_tip( $desc ) . '
			<input name="' . esc_attr( $id ) . '" id="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $value ) . '" class="colorpick" /> <div id="colorPickerDiv_' . esc_attr( $id ) . '" class="colorpickdiv"></div>
		</div>';
	}

	/**
	 * Notices.
	 */
	private function notices() {
		if ( isset( $_GET['section'] ) && 'keys' === $_GET['section'] ) {
			MemberHero_Admin_API_Keys::notices();
		}
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		if ( 'keys' === $current_section ) {
			MemberHero_Admin_API_Keys::page_output();
		} else {
			$settings = $this->get_settings( $current_section );
			MemberHero_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		MemberHero_Admin_Settings::save_fields( $settings );

		if ( $current_section ) {
			do_action( 'memberhero_update_options_' . $this->id . '_' . $current_section );
		}
	}

	/**
	 * Get settings array.
	 */
	public function get_settings( $current_section = '' ) {
		if ( 'account' === $current_section ) {
			$settings = apply_filters(
				'memberhero_account_page_settings', array(

					array(
						'title' => __( 'Account page', 'memberhero' ),
						'type'  => 'title',
						'desc'  => __( 'The account page needs to be set up so that users can edit their account information and settings.', 'memberhero' ),
						'id'    => 'account_page_options',
					),

					array(
						'title'    => __( 'My account page', 'memberhero' ),
						'desc'     => sprintf( __( 'Page contents: [%s]', 'memberhero' ), 'memberhero_account' ),
						'id'       => 'memberhero_account_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'memberhero-select',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Default Account Tab', 'memberhero' ),
						'id'       => 'memberhero_default_account_endpoint',
						'default'  => 'edit-account',
						'type'     => 'select',
						'class'    => 'memberhero-select',
						'options'  => memberhero_get_account_menu_items( array( 'logout' ) ),
						'desc'	   => __( 'This will be the default account tab when user access their account.', 'memberhero' ),
					),

					array(
						'type' => 'sectionend',
						'id'   => 'account_page_options',
					)

				)
			);
		} elseif ( 'profile' === $current_section ) {
			$settings = apply_filters(
				'memberhero_profile_page_settings', array(

					array(
						'title' => __( 'Profile page', 'memberhero' ),
						'type'  => 'title',
						'desc'  => __( 'The profile page set up enables a frontend profile for every users of your site.', 'memberhero' ),
						'id'    => 'profile_page_options',
					),

					array(
						'title'    => __( 'My profile page', 'memberhero' ),
						'desc'     => sprintf( __( 'Page contents: [%s]', 'memberhero' ), 'memberhero_profile' ),
						'id'       => 'memberhero_profile_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'memberhero-select',
						'desc_tip' => true,
					),

					array(
						'type' => 'sectionend',
						'id'   => 'profile_page_options',
					)

				)
			);
		} else {
			$settings = apply_filters(
				'memberhero_page_setup_options', array(

					array(
						'title' => __( 'Page setup', 'memberhero' ),
						'type'  => 'title',
						'desc'  => sprintf( wp_kses_post( __( 'These are the default user pages on the frontend. Users will need these pages to register, log in or reset password.<br />
						You can also configure <a href="%s">Account</a> and <a href="%s">Profile</a> pages.', 'memberhero' ) ), add_query_arg( 'section', 'account' ), add_query_arg( 'section', 'profile' ) ),
						'id'    => 'page_setup_options',
					),

					array(
						'title'    => __( 'Login page', 'memberhero' ),
						'id'       => 'memberhero_login_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'memberhero-select',
					),

					array(
						'title'    => __( 'Registration page', 'memberhero' ),
						'id'       => 'memberhero_register_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'memberhero-select',
					),

					array(
						'title'    => __( 'Lost password page', 'memberhero' ),
						'id'       => 'memberhero_lostpassword_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'memberhero-select',
					),

					array(
						'title'    => __( 'Members directory page', 'memberhero' ),
						'id'       => 'memberhero_list_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'memberhero-select',
					),

					array(
						'type' => 'sectionend',
						'id'   => 'page_setup_options',
					)

				)
			);
		}

		return apply_filters( 'memberhero_get_settings_' . $this->id, $settings, $current_section );
	}

	/**
	 * Show settings for account endpoints.
	 */
	public function account_endpoint_setting( $settings ) {
		$endpoints = memberhero_get_account_endpoints();

		$settings[] = array(
				'title' => __( 'Account endpoints', 'memberhero' ),
				'type'  => 'title',
				'desc'  => __( 'These are the account page endpoints that allow users to view and edit a specific account tab.', 'memberhero' ),
				'id'    => 'account_endpoint_options',
		);

		foreach( $endpoints as $endpoint_id => $endpoint ) {
			$settings[] = array(
				'title'    => __( memberhero()->query->get_endpoint_title( $endpoint_id ) ),
				'id'       => 'memberhero_account_' . str_replace( '-', '_', $endpoint_id ) . '_endpoint',
				'default'  => $endpoints[ $endpoint_id ],
				'type'     => 'text',
			);
		}

		$settings[] = array(
				'type' => 'sectionend',
				'id'   => 'account_endpoint_options',
		);

		return $settings;
	}

}

return new MemberHero_Settings_Advanced();