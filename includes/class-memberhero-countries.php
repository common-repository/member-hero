<?php
/**
 * Countries.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Countries class.
 */
class MemberHero_Countries {

	/**
	 * Locales list.
	 */
	public $locale = array();

	/**
	 * List of address formats for locales.
	 */
	public $address_formats = array();

	/**
	 * Auto-load in-accessible properties on demand.
	 */
	public function __get( $key ) {
		if ( 'countries' === $key ) {
			return $this->get_countries();
		} elseif ( 'states' === $key ) {
			return $this->get_states();
		}
	}

	/**
	 * Get all countries.
	 */
	public function get_countries() {
		if ( empty( $this->countries ) ) {
			$this->countries = apply_filters( 'memberhero_countries', include memberhero()->plugin_path() . '/i18n/countries.php' );
			if ( apply_filters( 'memberhero_sort_countries', true ) ) {
				uasort( $this->countries, 'memberhero_ascii_uasort_comparison' );
			}
		}

		return $this->countries;
	}

	/**
	 * Get country name.
	 */
	public function get_country( $cc ) {
		if ( ! empty( $this->countries[ $cc ] ) ) {
			return $this->countries[ $cc ];
		}
		return ucfirst( $cc );
	}

	/**
	 * Get all continents.
	 */
	public function get_continents() {
		if ( empty( $this->continents ) ) {
			$this->continents = apply_filters( 'memberhero_continents', include memberhero()->plugin_path() . '/i18n/continents.php' );
		}

		return $this->continents;
	}

	/**
	 * Get the states for a country.
	 */
	public function get_states( $cc = null ) {
		if ( ! isset( $this->states ) ) {
			$this->states = apply_filters( 'memberhero_states', include memberhero()->plugin_path() . '/i18n/states.php' );
		}

		if ( ! is_null( $cc ) ) {
			return isset( $this->states[ $cc ] ) ? $this->states[ $cc ] : false;
		} else {
			return $this->states;
		}
	}

	/**
	 * Outputs the list of countries and states for use in dropdown boxes.
	 */
	public function country_dropdown_options( $selected_country = '', $selected_state = '', $escape = false ) {
		if ( $this->countries ) {
			foreach ( $this->countries as $key => $value ) {
				$states = $this->get_states( $key );
				if ( $states ) {
					echo '<optgroup label="' . esc_attr( $value ) . '">';
					foreach ( $states as $state_key => $state_value ) {
						echo '<option value="' . esc_attr( $key ) . ':' . esc_attr( $state_key ) . '"';

						if ( $selected_country === $key && $selected_state === $state_key ) {
							echo ' selected="selected"';
						}

						echo '>' . esc_html( $value ) . ' &mdash; ' . ( $escape ? esc_js( $state_value ) : $state_value ) . '</option>'; // WPCS: XSS ok.
					}
					echo '</optgroup>';
				} else {
					echo '<option';
					if ( $selected_country === $key && '*' === $selected_state ) {
						echo ' selected="selected"';
					}
					echo ' value="' . esc_attr( $key ) . '">' . ( $escape ? esc_js( $value ) : $value ) . '</option>'; // WPCS: XSS ok.
				}
			}
		}
	}

}