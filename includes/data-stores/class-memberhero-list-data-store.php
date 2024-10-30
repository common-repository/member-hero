<?php
/**
 * Member List Data Store.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_List_Data_Store class.
 */
class MemberHero_List_Data_Store extends MemberHero_Data_Store_WP {

	/**
	 * Search member directories.
	 */
	public function search_lists( $term, $all_statuses = false, $limit = null ) {
		global $wpdb;

		$post_types    = array( 'memberhero_list' );
		$post_statuses = array( 'publish' );
		$type_join     = '';
		$type_where    = '';
		$status_where  = '';
		$limit_query   = '';
		$term          = memberhero_strtolower( $term );

		// See if search term contains OR keywords.
		if ( strstr( $term, ' or ' ) ) {
			$term_groups = explode( ' or ', $term );
		} else {
			$term_groups = array( $term );
		}

		$search_where   = '';
		$search_queries = array();

		foreach ( $term_groups as $term_group ) {
			// Parse search terms.
			if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $term_group, $matches ) ) {
				$search_terms = $this->get_valid_search_terms( $matches[0] );
				$count        = count( $search_terms );

				// if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence.
				if ( 9 < $count || 0 === $count ) {
					$search_terms = array( $term_group );
				}
			} else {
				$search_terms = array( $term_group );
			}

			$term_group_query = '';
			$searchand        = '';

			foreach ( $search_terms as $search_term ) {
				$like              = '%' . $wpdb->esc_like( $search_term ) . '%';
				$term_group_query .= $wpdb->prepare( " {$searchand} ( 
						( posts.post_title LIKE %s) 
				)", $like );
				$searchand         = ' AND ';
			}

			if ( $term_group_query ) {
				$search_queries[] = $term_group_query;
			}
		}

		if ( ! empty( $search_queries ) ) {
			$search_where = 'AND (' . implode( ') OR (', $search_queries ) . ')';
		}

		if ( ! $all_statuses ) {
			$status_where = " AND posts.post_status IN ('" . implode( "','", $post_statuses ) . "') ";
		}

		if ( $limit ) {
			$limit_query = $wpdb->prepare( ' LIMIT %d ', $limit );
		}

		$search_results = $wpdb->get_results(
			"SELECT DISTINCT posts.ID as list_id, posts.post_parent as parent_id FROM {$wpdb->posts} posts
			LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
			$type_join
			WHERE posts.post_type IN ('" . implode( "','", $post_types ) . "')
			$search_where
			$status_where
			$type_where
			ORDER BY posts.post_parent ASC, posts.post_title ASC
			$limit_query
			"
		);

		// Get post ids only.
		$post_ids = wp_list_pluck( $search_results, 'list_id' );

		return $post_ids;
	}

}