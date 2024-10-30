<?php
/**
 * General Settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'MemberHero_Settings_General', false ) ) {
	return new MemberHero_Settings_General();
}

/**
 * MemberHero_Settings_General class.
 */
class MemberHero_Settings_General extends MemberHero_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'memberhero' );

		parent::__construct();
		$this->notices();
	}

	/**
	 * Get sections.
	 */
	public function get_sections() {
		$sections = array(
			''             => __( 'General options', 'memberhero' ),
			'security'	   => __( 'Security', 'memberhero' ),
			'uploads'	   => __( 'Uploads', 'memberhero' ),
			'import'	   => __( 'Import users', 'memberhero' ),
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
		if ( isset( $_GET['section'] ) && 'import' === $_GET['section'] ) {
			MemberHero_Admin_Import_Users::notices();
		}
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		if ( 'import' === $current_section ) {
			MemberHero_Admin_Import_Users::page_output();
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

		// Get the old cron value before its saved.
		$delete_recurrence_pre = get_option( 'memberhero_delete_users_event_recurrence' );

		$settings = $this->get_settings( $current_section );
		MemberHero_Admin_Settings::save_fields( $settings );

		if ( $current_section ) {

			do_action( 'memberhero_update_options_' . $this->id . '_' . $current_section );

		} else {

			// Update the cron schedule for deleting rejected users.
			$delete_recurrence = get_option( 'memberhero_delete_users_event_recurrence' );
			if ( $delete_recurrence != $delete_recurrence_pre ) {
				wp_clear_scheduled_hook( 'memberhero_delete_rejected_users' );
				if ( $delete_recurrence != '' ) {
					wp_schedule_event( time() + 10, $delete_recurrence, 'memberhero_delete_rejected_users' );
				}
			}

		}
	}

	/**
	 * Get settings array.
	 */
	public function get_settings( $current_section = '' ) {
		if ( 'security' === $current_section ) {
			$settings = apply_filters(
				'memberhero_security_settings', array(

					array(
						'title' => __( 'Admin security', 'memberhero' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'general_security_options',
					),

					array(
						'title'    => __( 'WP admin registration', 'memberhero' ),
						'desc'     => __( 'Block the WordPress admin registration screen', 'memberhero' ),
						'id'       => 'memberhero_block_wpregister',
						'default'  => 'no',
						'type'     => 'checkbox',
						'desc_tip' => __( 'When enabled, users will no longer be able to register via WordPress admin screen.<br />Instead, users will be redirected to the front-end registration page set by the plugin.', 'memberhero' ),
					),

					array(
						'title'    => __( 'WP admin login', 'memberhero' ),
						'desc'     => __( 'Block the WordPress admin login screen', 'memberhero' ),
						'id'       => 'memberhero_block_wplogin',
						'default'  => 'no',
						'type'     => 'checkbox',
						'desc_tip' => __( 'When enabled, users will no longer be able to access the WordPress admin login screen.<br />Be careful to avoid lock out, a default login form and page must be setup already where users will get redirected.', 'memberhero' ),
					),

					array(
						'title'    => __( 'WP admin password reset', 'memberhero' ),
						'desc'     => __( 'Block the WordPress admin password reset screen', 'memberhero' ),
						'id'       => 'memberhero_block_wplostpassword',
						'default'  => 'no',
						'type'     => 'checkbox',
						'desc_tip' => __( 'When enabled, users will no longer be able to reset their password via the WordPress admin screen.<br />Instead, users will be redirected to the front-end password reset page set by the plugin.', 'memberhero' ),
					),

					array(
						'type' => 'sectionend',
						'id'   => 'general_security_options',
					)

				)
			);
		} elseif ( 'uploads' === $current_section ) {
			$settings = apply_filters( 'memberhero_upload_settings', array(

				array(
					'title' => __( 'Upload settings', 'memberhero' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'upload_options',
				),

				array(
					'title'             => __( 'Avatar upload quality', 'memberhero' ),
					'desc'              => __( 'Enter the quality in which avatar photos will be uploaded. The higher, the better quality and bigger file size.', 'memberhero' ),
					'id'                => 'memberhero_avatar_upload_quality',
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => 25,
						'step' => 1,
						'max'  => 100,
						),
					'css'               => 'width: 80px;',
					'default'           => '100',
				),

				array(
					'title'             => __( 'Header upload quality', 'memberhero' ),
					'desc'              => __( 'Enter the quality in which header photos will be uploaded. The higher, the better quality and bigger file size.', 'memberhero' ),
					'id'                => 'memberhero_cover_upload_quality',
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => 25,
						'step' => 1,
						'max'  => 100,
						),
					'css'               => 'width: 80px;',
					'default'           => '75',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'upload_options',
				),

				array(
					'title' => __( 'Importing from URL', 'memberhero' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'import_url_options',
				),

				array(
					'title'    => __( 'Remote URL uploads', 'memberhero' ),
					'desc'     => __( 'Allow users to import profile avatars and headers from a remote URL', 'memberhero' ),
					'id'       => 'memberhero_uploads_via_url',
					'default'  => 'yes',
					'type'     => 'checkbox',
					'premium'  => 'yes',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'import_url_options',
				),

			) );
		} else {
			$settings = apply_filters(
				'memberhero_general_settings', array(

					array(
						'title' => __( 'Registration options', 'memberhero' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'registration_options',
					),

					array(
						'title'    => __( 'New user default role', 'memberhero' ),
						'desc'     => __( 'This option lets you define the default role for new users registered through Member Hero.', 'memberhero' ),
						'id'       => 'memberhero_default_role',
						'default'  => 'member',
						'type'     => 'select',
						'class'    => 'memberhero-select short',
						'options'  => memberhero_get_roles(),
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Email confirmation', 'memberhero' ),
						'desc'     => __( 'Require new users to confirm their email', 'memberhero' ),
						'id'       => 'memberhero_email_confirm',
						'default'  => 'no',
						'type'     => 'checkbox',
						'desc_tip' => __( 'New accounts will be required to confirm their email. This setting can be overwritten by user roles.', 'memberhero' ),
					),

					array(
						'title'    => __( 'Admin approval', 'memberhero' ),
						'desc'     => __( 'Manually approve new user accounts', 'memberhero' ),
						'id'       => 'memberhero_manual_approval',
						'default'  => 'no',
						'type'     => 'checkbox',
						'desc_tip' => __( 'New accounts will require admin approval. This setting can be overwritten by user roles.', 'memberhero' ),
					),

					array(
						'type' => 'sectionend',
						'id'   => 'registration_options',
					),

					array(
						'title' => __( 'User Profiles', 'memberhero' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'profile_options',
					),

					array(
						'title'    => __( 'Profile privacy', 'memberhero' ),
						'desc'     => __( 'Require users to log in to view profiles', 'memberhero' ),
						'id'       => 'memberhero_disable_public_profiles',
						'default'  => 'no',
						'type'     => 'checkbox',
						'desc_tip' => __( 'Users must log in to view other user profiles. Check this to prevent non-logged users from viewing profiles.', 'memberhero' ),
					),

					array(
						'type' => 'sectionend',
						'id'   => 'profile_options',
					),

					array(
						'title' => __( 'Accounts', 'memberhero' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'account_options',
					),

					array(
						'title'    => __( 'Username changes', 'memberhero' ),
						'desc'     => __( 'Allow username changes in account page', 'memberhero' ),
						'id'       => 'memberhero_allow_user_login_change',
						'default'  => 'no',
						'type'     => 'checkbox',
						'desc_tip' => __( 'Users can edit their username through the account page.', 'memberhero' ),
					),

					array(
						'title'    => __( 'Automatically delete rejected users', 'memberhero' ),
						'id'       => 'memberhero_delete_users_event_recurrence',
						'default'  => '6_hours',
						'type'     => 'select',
						'class'    => 'memberhero-select short',
						'options'  => memberhero_get_schedules(),
					),

					array(
						'title'    => __( 'Automatically delete users who have not confirmed email in', 'memberhero' ),
						'id'       => 'memberhero_delete_unconfirmed_emails_duration',
						'default'  => 6,
						'type'     => 'text',
						'css'	   => 'width:60px;',
						'desc'	   => _x( 'hour(s) (Leave blank to never delete accounts)', 'account settings', 'memberhero' ),
					),

					array(
						'title'       => __( 'Post account deletion URL', 'memberhero' ),
						'desc'        => __( 'Send the user to a custom URL when they delete their account. Leave blank to redirect to homepage.', 'memberhero' ),
						'id'          => 'memberhero_post_delete_url',
						'type'        => 'text',
						'placeholder' => trailingslashit( home_url() ),
						'default'     => '',
						'autoload'    => false,
					),

					array(
						'type' => 'sectionend',
						'id'   => 'account_options',
					),

					array(
						'title' => __( 'Setup', 'memberhero' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'setup_options',
					),

					array(
						'title'    => __( 'Rerun Setup Wizard', 'memberhero' ),
						'desc'     => __( 'You can use this button to re-run the setup wizard.', 'memberhero' ),
						'type'     => 'button',
						'text'	   => __( 'Setup Wizard', 'memberhero' ),
						'url'	   => esc_url( admin_url( 'admin.php?page=memberhero-setup' ) ),
					),

					array(
						'type' => 'sectionend',
						'id'   => 'setup_options',
					),

				)
			);
		}

		return apply_filters( 'memberhero_get_settings_' . $this->id, $settings, $current_section );
	}

}

return new MemberHero_Settings_General();