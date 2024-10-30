<?php
/**
 * Custom Field Core.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MemberHero_Abstract_Post', false ) ) {
	include_once 'abstracts/abstract-class-memberhero-post.php';
}

/**
 * MemberHero_Field class.
 */
class MemberHero_Field extends MemberHero_Abstract_Post {

	/**
	 * Post type.
	 */
	public $post_type = 'memberhero_field';

	/**
	 * Meta keys.
	 */
	public $internal_meta_keys = array(
		'key',
		'type',
		'icon',
		'label',
		'edit_label',
		'view_label',
		'placeholder',
		'helper',
		'custom_error',
		'can_view',
		'is_readonly',
		'is_private',
		'is_required',
		'dropdown_options',
		'checkbox_options',
		'radio_options',
		'blocked_emails',
		'allowed_emails',
		'mimes',
		'emojis',
		'autolinks',
		'enable_decimals',
		'min_num',
		'max_num',
		'show_hints',
		'ratings',
		'no_input',
		'value',
		'delete_file',
	);

	/**
	 * Check if field key exists.
	 */
	public function exists( $key ) {
		if ( ! is_array( $fields = get_option( 'memberhero_fields' ) ) ) {
			return false;
		}
		return array_key_exists( $key, $fields );
	}

	/**
	 * Custom save action.
	 */
	public function _save( $props ) {
		if ( $props['key'] == '' ) {
			return;
		}

		$fields = get_option( 'memberhero_fields' );
		foreach( $props as $key => $value ) {
			$fields[$props['key']][$key] = $value;
		}

		update_option( 'memberhero_fields', $fields );
	}

	/**
	 * When this item is deleted.
	 */
	public function _delete() {
		$fields = get_option( 'memberhero_fields' );
		if ( is_array( $fields ) && array_key_exists( $this->key, $fields ) ) {
			unset( $fields[ $this->key ] );
			update_option( 'memberhero_fields', $fields );
		}
	}

}