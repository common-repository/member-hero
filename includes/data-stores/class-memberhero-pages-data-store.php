<?php
/**
 * Pages Data Store.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Pages_Data_Store class.
 */
class MemberHero_Pages_Data_Store extends MemberHero_Data_Store_WP {

	/**
	 * Search pages.
	 */
	public function search_pages( $term ) {
		$results = array();

		$args = array(
			'post_type' 		=> 'page',
			's' 				=> $term,
			'post_status' 		=> 'publish',
			'orderby'     		=> 'title', 
			'order'       		=> 'asc',
			'posts_per_page' 	=> -1
		);

		$wp_query = new WP_Query( $args );

		$posts = $wp_query->posts;

		if ( ! empty( $posts ) ) {
			foreach( $posts as $post ) {
				$results[] = array(
					'id'	=> $post->ID,
					'title'	=> $post->post_title,
				);
			}
		}

		return $results;
	}

}