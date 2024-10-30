<?php
/**
 * Addon Settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'MemberHero_Settings_Addons', false ) ) {
	return new MemberHero_Settings_Addons();
}

/**
 * MemberHero_Settings_Addons class.
 */
class MemberHero_Settings_Addons extends MemberHero_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'addons';
		$this->label = __( 'Addons', 'memberhero' );

		parent::__construct();
	}

	/**
	 * Get sections.
	 */
	public function get_sections() {
		$sections = apply_filters( 'memberhero_get_sections_' . $this->id, array() );

		asort( $sections );

		return $sections;
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

		if ( 'drip' === $current_section ) {
			MHDC_Admin_Dripping::page_output();
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
		$settings = array();

		if ( $current_section == '' ) {
			$settings = apply_filters(
				'memberhero_addon_settings', array(

				)
			);
		}

		return apply_filters( 'memberhero_get_settings_' . $this->id, $settings, $current_section );
	}

}

return new MemberHero_Settings_Addons();