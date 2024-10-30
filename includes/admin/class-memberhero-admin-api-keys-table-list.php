<?php
/**
 * Member Hero API Keys Table List
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * MemberHero_Admin_API_Keys_Table_List class.
 */
class MemberHero_Admin_API_Keys_Table_List extends WP_List_Table {

	/**
	 * Initialize the API key table list.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'key',
				'plural'   => 'keys',
				'ajax'     => false,
			)
		);
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'No keys found.', 'memberhero' );
	}

	/**
	 * Get list columns.
	 */
	public function get_columns() {
		return array(
			'cb'            => '<input type="checkbox" />',
			'title'         => __( 'Description', 'memberhero' ),
			'truncated_key' => __( 'Consumer key ending in', 'memberhero' ),
			'user'          => __( 'User', 'memberhero' ),
			'permissions'   => __( 'Permissions', 'memberhero' ),
			'last_access'   => __( 'Last access', 'memberhero' ),
		);
	}

	/**
	 * Column cb.
	 */
	public function column_cb( $key ) {
		return sprintf( '<input type="checkbox" name="key[]" value="%1$s" />', $key['key_id'] );
	}

	/**
	 * Return title column.
	 */
	public function column_title( $key ) {
		$url     = admin_url( 'admin.php?page=memberhero-settings&tab=advanced&section=keys&edit-key=' . $key['key_id'] );
		$user_id = intval( $key['user_id'] );

		// Check if current user can edit other users or if it's the same user.
		$can_edit = current_user_can( 'edit_user', $user_id ) || get_current_user_id() === $user_id;

		$output = '<strong>';
		if ( $can_edit ) {
			$output .= '<a href="' . esc_url( $url ) . '" class="row-title">';
		}
		if ( empty( $key['description'] ) ) {
			$output .= __( 'API key', 'memberhero' );
		} else {
			$output .= esc_html( $key['description'] );
		}
		if ( $can_edit ) {
			$output .= '</a>';
		}
		$output .= '</strong>';

		// Get actions.
		$actions = array(
			/* translators: %s: API key ID. */
			'id' => sprintf( __( 'ID: %d', 'memberhero' ), $key['key_id'] ),
		);

		if ( $can_edit ) {
			$actions['edit']  = '<a href="' . esc_url( $url ) . '">' . __( 'View/Edit', 'memberhero' ) . '</a>';
			$actions['trash'] = '<a class="submitdelete" aria-label="' . esc_attr__( 'Revoke API key', 'memberhero' ) . '" href="' . esc_url(
				wp_nonce_url(
					add_query_arg(
						array(
							'revoke-key' => $key['key_id'],
						), admin_url( 'admin.php?page=memberhero-settings&tab=advanced&section=keys' )
					), 'revoke'
				)
			) . '">' . __( 'Revoke', 'memberhero' ) . '</a>';
		}

		$row_actions = array();

		foreach ( $actions as $action => $link ) {
			$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
		}

		$output .= '<div class="row-actions">' . implode( ' | ', $row_actions ) . '</div>';

		return $output;
	}

	/**
	 * Return truncated consumer key column.
	 *
	 * @param  array $key Key data.
	 * @return string
	 */
	public function column_truncated_key( $key ) {
		return '<code>&hellip;' . esc_html( $key['truncated_key'] ) . '</code>';
	}

	/**
	 * Return user column.
	 */
	public function column_user( $key ) {
		$user = get_user_by( 'id', $key['user_id'] );

		if ( ! $user ) {
			return '';
		}

		if ( current_user_can( 'edit_user', $user->ID ) ) {
			return '<a href="' . esc_url( add_query_arg( array( 'user_id' => $user->ID ), admin_url( 'user-edit.php' ) ) ) . '">' . esc_html( $user->display_name ) . '</a>';
		}

		return esc_html( $user->display_name );
	}

	/**
	 * Return permissions column.
	 */
	public function column_permissions( $key ) {
		$permission_key = $key['permissions'];
		$permissions    = array(
			'read'       => __( 'Read', 'memberhero' ),
			'write'      => __( 'Write', 'memberhero' ),
			'read_write' => __( 'Read/Write', 'memberhero' ),
		);

		if ( isset( $permissions[ $permission_key ] ) ) {
			return esc_html( $permissions[ $permission_key ] );
		} else {
			return '';
		}
	}

	/**
	 * Return last access column.
	 */
	public function column_last_access( $key ) {
		if ( ! empty( $key['last_access'] ) ) {
			/* translators: 1: last access date 2: last access time */
			$last_access = strtotime( get_date_from_gmt( $key['last_access'] ) );
			$date = sprintf( __( '%1$s at %2$s', 'memberhero' ), date_i18n( memberhero_date_format(), $last_access ), date_i18n( memberhero_time_format(), $last_access ) );

			return apply_filters( 'memberhero_api_key_last_access_datetime', $date, $key['last_access'] );
		}

		return __( 'Unknown', 'memberhero' );
	}

	/**
	 * Get bulk actions.
	 */
	protected function get_bulk_actions() {
		if ( ! current_user_can( 'remove_users' ) ) {
			return array();
		}

		return array(
			'revoke' => __( 'Revoke', 'memberhero' ),
		);
	}

	/**
	 * Search box.
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
			return;
		}

		$input_id     = $input_id . '-search-input';
		$search_query = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';

		echo '<p class="search-box">';
		echo '<label class="screen-reader-text" for="' . esc_attr( $input_id ) . '">' . esc_html( $text ) . ':</label>';
		echo '<input type="search" id="' . esc_attr( $input_id ) . '" name="s" value="' . esc_attr( $search_query ) . '" />';
		submit_button(
			$text, '', '', false,
			array(
				'id' => 'search-submit',
			)
		);
		echo '</p>';
	}

	/**
	 * Prepare table list items.
	 */
	public function prepare_items() {
		global $wpdb;

		$per_page     = $this->get_items_per_page( 'memberhero_keys_per_page' );
		$current_page = $this->get_pagenum();

		if ( 1 < $current_page ) {
			$offset = $per_page * ( $current_page - 1 );
		} else {
			$offset = 0;
		}

		$search = '';

		if ( ! empty( $_REQUEST['s'] ) ) {
			$search = "AND description LIKE '%" . esc_sql( $wpdb->esc_like( memberhero_clean( wp_unslash( $_REQUEST['s'] ) ) ) ) . "%' ";
		}

		// Get the API keys.
		$keys = $wpdb->get_results(
			"SELECT key_id, user_id, description, permissions, truncated_key, last_access FROM {$wpdb->prefix}memberhero_api_keys WHERE 1 = 1 {$search}" .
			$wpdb->prepare( 'ORDER BY key_id DESC LIMIT %d OFFSET %d;', $per_page, $offset ), ARRAY_A
		);

		$count = $wpdb->get_var( "SELECT COUNT(key_id) FROM {$wpdb->prefix}memberhero_api_keys WHERE 1 = 1 {$search};" );

		$this->items = $keys;

		// Set the pagination.
		$this->set_pagination_args(
			array(
				'total_items' => $count,
				'per_page'    => $per_page,
				'total_pages' => ceil( $count / $per_page ),
			)
		);
	}

}