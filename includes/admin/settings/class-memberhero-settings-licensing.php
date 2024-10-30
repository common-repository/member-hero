<?php
/**
 * Licensing Settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'MemberHero_Settings_Licensing', false ) ) {
	return new MemberHero_Settings_Licensing();
}

/**
 * MemberHero_Settings_Licensing class.
 */
class MemberHero_Settings_Licensing extends MemberHero_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'licensing';
		$this->label = __( 'Licensing', 'memberhero' );

		parent::__construct();
	}

	/**
	 * Get sections.
	 */
	public function get_sections() {
		$sections = array(
			''             => __( 'Licensing', 'memberhero' ),
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

		do_action( 'memberhero_saved_license_settings' );
	}

	/**
	 * Get settings array.
	 */
	public function get_settings( $current_section = '' ) {
		$settings = apply_filters(
			'memberhero_license_settings', array(

				array(
					'title' => __( 'Licensing', 'memberhero' ),
					'type'  => 'title',
					'desc'  => __( 'Enter your add-on license keys here to receive updates for purchased add-ons. If your license key has expired, please renew your license.', 'memberhero' ),
					'id'    => 'license_options',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'license_options',
				),

			)
		);

		return apply_filters( 'memberhero_get_settings_' . $this->id, $settings, $current_section );
	}

}

return new MemberHero_Settings_Licensing();