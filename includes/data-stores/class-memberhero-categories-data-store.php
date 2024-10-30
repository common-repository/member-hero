<?php
/**
 * Categories Data Store.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Categories_Data_Store class.
 */
class MemberHero_Categories_Data_Store extends MemberHero_Data_Store_WP {

	/**
	 * Search categories.
	 */
	public function search_categories( $term ) {
		$results = array();

		$terms = get_terms( 'category', array(
			'name__like' => $term,
			'hide_empty' => true // Optional 
		) );

		if ( ! empty( $terms ) ) {
			foreach( $terms as $term ) {
				$results[] = array(
					'id'	=> $term->term_id,
					'title'	=> $term->name,
				);
			}
		}

		return $results;
	}

}