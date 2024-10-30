<?php
/**
 * Customization Settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'MemberHero_Settings_Profiles', false ) ) {
	return new MemberHero_Settings_Profiles();
}

/**
 * MemberHero_Settings_Profiles class.
 */
class MemberHero_Settings_Profiles extends MemberHero_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'profiles';
		$this->label = __( 'Profiles', 'memberhero' );

		parent::__construct();
	}

	/**
	 * Get sections.
	 */
	public function get_sections() {
		$sections = array(
			''             => __( 'Customization', 'memberhero' ),
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
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		$settings = $this->get_settings( $current_section );

		MemberHero_Admin_Settings::output_fields( $settings );
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
		$settings = apply_filters(
			'memberhero_customization_settings', array(

				array(
					'title' => __( 'Profile fields', 'memberhero' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'profile_fields',
				),

				array(
					'title'    => __( 'Auto links in user description', 'memberhero' ),
					'desc'     => __( 'Automatically convert URLs to clickable links in user description', 'memberhero' ),
					'id'       => 'memberhero_autolinks',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'profile_fields',
				),

				array(
					'title' => __( 'Profile tabs', 'memberhero' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'profile_tabs',
				),

				array(
					'title'    => __( 'User posts tab', 'memberhero' ),
					'desc'     => __( 'Show the user posts under their profile', 'memberhero' ),
					'id'       => 'memberhero_posts_tab',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),

				array(
					'title'    => __( 'User comments tab', 'memberhero' ),
					'desc'     => __( 'Show the user comments under their profile', 'memberhero' ),
					'id'       => 'memberhero_comments_tab',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'profile_tabs',
				)

			)
		);

		return apply_filters( 'memberhero_get_settings_' . $this->id, $settings, $current_section );
	}

}

return new MemberHero_Settings_Profiles();