<?php
/**
 * Posts Data Store.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Posts_Data_Store class.
 */
class MemberHero_Posts_Data_Store extends MemberHero_Data_Store_WP {

	/**
	 * Search posts.
	 */
	public function search_posts( $term ) {
		$results = array();

		$args = array(
			'post_type' 		=> 'post',
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