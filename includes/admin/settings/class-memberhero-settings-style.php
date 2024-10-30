<?php
/**
 * Style Settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'MemberHero_Settings_Style', false ) ) {
	return new MemberHero_Settings_Style();
}

/**
 * MemberHero_Settings_Style class.
 */
class MemberHero_Settings_Style extends MemberHero_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'style';
		$this->label = __( 'Style', 'memberhero' );

		parent::__construct();
	}

	/**
	 * Get sections.
	 */
	public function get_sections() {
		$sections = array(
			''             => __( 'Style', 'memberhero' ),
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

		if ( ! $current_section ) {
			$frontend_styles = MemberHero_Frontend_Scripts::init();
		} else {
			$settings = $this->get_settings( $current_section );
			MemberHero_Admin_Settings::save_fields( $settings );

			if ( $current_section ) {
				do_action( 'memberhero_update_options_' . $this->id . '_' . $current_section );
			}
		}
	}

	/**
	 * Get settings array.
	 */
	public function get_settings( $current_section = '' ) {
		$settings = apply_filters(
			'memberhero_style_settings', array(

				array(
					'title' => __( 'Color options', 'memberhero' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'color_options',
				),

				array(
					'title'    => __( 'Base color', 'memberhero' ),
					'desc'     => sprintf( wp_kses_post( __( 'The primary or base color for Member Hero templates. Default %s.', 'memberhero' ) ), '<code>#027FD2</code>' ),
					'id'       => 'memberhero_primary_bg_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#027FD2',
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Primary text color', 'memberhero' ),
					'desc'     => sprintf( wp_kses_post( __( 'The primary text color for Member Hero templates. Default %s.', 'memberhero' ) ), '<code>#66757f</code>' ),
					'id'       => 'memberhero_primary_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#66757f',
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Alert color', 'memberhero' ),
					'desc'     => sprintf( wp_kses_post( __( 'The alert color for Member Hero templates. Default %s.', 'memberhero' ) ), '<code>#bb0000</code>' ),
					'id'       => 'memberhero_alert_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#bb0000',
					'desc_tip' => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'color_options',
				)

			)
		);

		return apply_filters( 'memberhero_get_settings_' . $this->id, $settings, $current_section );
	}

}

return new MemberHero_Settings_Style();