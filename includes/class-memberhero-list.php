<?php
/**
 * Member list Core.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MemberHero_Abstract_Post', false ) ) {
	include_once 'abstracts/abstract-class-memberhero-post.php';
}

/**
 * MemberHero_List class.
 */
class MemberHero_List extends MemberHero_Abstract_Post {

	/**
	 * Post type.
	 */
	public $post_type = 'memberhero_list';

	/**
	 * Meta keys.
	 */
	public $internal_meta_keys = array(
		'per_page',
		'login_required',
		'use_ajax',
		'roles',
		'orderby',
		'show_menu',
		'show_social',
		'show_bio',
		'centered',
	);

	/**
	 * Stores extra filters.
	 */
	public $memberhero_filters = array();

	/**
	 * Defines member list is in loop.
	 */
	public function in_loop() {
		$this->_in_loop = true;
	}

	/**
	 * Get page.
	 */
	public function get_page() {
		return ! empty( $_REQUEST[ 'wpage' ] ) && absint( $_REQUEST[ 'wpage' ] ) > 0 ? absint( $_REQUEST[ 'wpage' ] ) : 1;
	}

	/**
	 * Get sorting parameter.
	 */
	public function get_sort() {
		return ! empty( $_REQUEST[ 'wsort' ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ 'wsort' ] ) ) : '';
	}

	/**
	 * Get search query.
	 */
	public function get_search() {
		global $wpdb;

		$q = ! empty( $_REQUEST[ 'ws' ] ) ? esc_attr( $_REQUEST[ 'ws' ] ) : '';

		return ! empty( $q ) ? '*' . $q . '*' : '';
	}

	/**
	 * Get search filters.
	 */
	public function get_filters() {
		$query_filters 	= ! empty( $_REQUEST[ 'wfilters' ] ) ? memberhero_encoded_str_to_array( $_REQUEST[ 'wfilters' ] ) : array();
		$memberhero_filters 	= apply_filters( 'memberhero_list_custom_filters', $this->memberhero_filters );

		return array_merge( array_filter( $memberhero_filters ), array_filter( $query_filters ) );
	}

}