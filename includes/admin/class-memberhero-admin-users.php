<?php
/**
 * Users Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Admin_Users class.
 */
class MemberHero_Admin_Users {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'user_row_actions', array( $this, 'user_row_actions' ), 10, 2 );

		add_filter( 'manage_users_columns', array( $this, 'add_custom_columns' ), 99 );
		add_action( 'manage_users_custom_column', array( $this, 'view_custom_columns' ), 99, 3 );
		add_filter( 'manage_users_sortable_columns', array( $this, 'define_sortable_columns' ), 99 );

		add_action( 'pre_user_query', array( $this, 'order_users_by_date' ), 99 );
		add_action( 'pre_user_query', array( $this, 'order_users_by_column' ), 102 );

		// Add status filter.
		add_action( 'restrict_manage_users', array( $this, 'show_status_filter' ) );
		add_filter( 'pre_get_users', array( $this, 'filter_by_status' ) );

		// Action Hooks.
		add_action( 'delete_user', array( $this, 'delete_user_data' ) );
	}

	/**
	 * User row actions.
	 */
	public function user_row_actions( $actions, $user ) {
		$actions[ 'view' ] = '<a href="' . memberhero_get_profile_url( $user->user_login ) . '" aria-label="' . __( 'View user profile', 'memberhero' ) . '">' . __( 'View', 'memberhero' ) . '</a>';

		return $actions;
	}

	/**
	 * Add custom columns.
	 */
	public function add_custom_columns( $columns ) {
		$checkbox = $columns[ 'cb' ];
		unset( $columns[ 'cb' ] );

		$columns[ 'memberhero_date' ] 	  = _x( 'Registered', 'user', 'memberhero' );
		$columns[ 'memberhero_actions' ] = '';

		return array_merge( array( 'cb' => $checkbox, 'memberhero_status' => '<span class="memberhero_status tips" data-tip="' . esc_attr( 'Status', 'memberhero' ) . '">' . memberhero_svg_icon( 'more-horizontal' ) . '</span>' ), $columns );
	}

	/**
	 * Control the output of custom columns.
	 */
	public function view_custom_columns( $value, $column_name, $user_id ) {

		// Status
		if ( 'memberhero_status' == $column_name ) {
			if ( is_memberhero_unconfirmed_email_user( $user_id ) ) {
				$html = '<span class="status-email tips" data-tip="' . esc_attr__( 'Unconfirmed', 'memberhero' ) . '">' . memberhero_svg_icon( 'mail' ) . '</span>';
			} elseif ( is_memberhero_rejected_user( $user_id ) ) {
				$html = '<span class="status-rejected tips" data-tip="' . esc_attr__( 'Rejected', 'memberhero' ) . '">' . memberhero_svg_icon( 'x' ) . '</span>';
			} elseif ( is_memberhero_pending_user( $user_id ) ) {
				$html = '<span class="status-pending tips" data-tip="' . esc_attr__( 'Pending', 'memberhero' ) . '">' . memberhero_svg_icon( 'more-horizontal' ) . '</span>';
			} else {
				$html = '<span class="status-active tips" data-tip="' . esc_attr__( 'Active', 'memberhero' ) . '">' . memberhero_svg_icon( 'check' ) . '</span>';
			}

			return $html;
		}

		// Date registered.
		if ( 'memberhero_date' == $column_name ) {
			$user = get_userdata( $user_id );

			$registered = strtotime( get_date_from_gmt( $user->user_registered ) );

			$date = sprintf( __( '%1$s at %2$s', 'memberhero' ), date_i18n( memberhero_date_format(), $registered ), date_i18n( memberhero_time_format(), $registered ) );
	
			return $date;
		}

		// Actions.
		if ( 'memberhero_actions' == $column_name ) {
			$output  = null;
			$actions = $this->get_user_actions( $user_id );

			if ( empty( $actions ) ) {
				return;
			}

			foreach( $actions as $action ) {
				$output .= $action;
			}

			$output .= '<span class="spinner">' . __( 'Processing...', 'memberhero' ) . '</span>';
			$output .= '<span class="updater">' . __( 'Action completed', 'memberhero' ) . '</span>';

			return $output;
		}

		return $value;
	}

	/**
	 * Recent users to appear first.
	 */
	public function order_users_by_date( $query ) {
		global $pagenow;

		if ( ! is_admin() || 'users.php' !== $pagenow || isset( $_GET[ 'orderby' ] ) ) {
			return;
		}

		$query->query_orderby = 'ORDER BY user_registered DESC';
	}

	/**
	 * Ordering by custom criteria.
	 */
	public function order_users_by_column( $query ) {
		global $pagenow, $wpdb;

		if ( is_admin() && 'users.php' === $pagenow && isset( $_GET[ 'orderby' ] ) ) {

			$orderby = esc_attr( $_GET[ 'orderby' ] );
			$order   = isset( $_GET[ 'order' ] ) ? esc_attr( $_GET[ 'order' ] ) : 'desc';

			switch( $orderby ) {
				case 'role' :
					$query->query_from .= " JOIN {$wpdb->usermeta} wpmeta ON wpmeta.user_id = {$wpdb->users}.ID AND wpmeta.meta_key = 'wp_capabilities'";
					$query->query_orderby = 'ORDER by REPLACE( wpmeta.meta_value, SUBSTRING_INDEX( wpmeta.meta_value, \'"\', 1 ), \'\' ) ' . $order;
				break;
			}
		}

	}

	/**
	 * Makes the column sortable
	 */
	public function define_sortable_columns( $columns ) {
		$custom = array(
			'memberhero_date'	=> 'registered',
			'role'		=> 'role',
		);
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Get the user actions buttons.
	 */
	public function get_user_actions( $user_id = 0 ) {
		$actions = array();

		if ( user_can( $user_id, 'manage_memberhero' ) ) {
			return;
		}

		if ( ! is_memberhero_unconfirmed_email_user( $user_id ) ) {
			$actions[] = '<a href="' . wp_nonce_url( admin_url( 'admin-ajax.php?action=memberhero_confirm_email&id=' . $user_id ), 'memberhero-confirm-email' ) . '" class="button memberhero_ajax memberhero-top-tips" data-tip="' . esc_attr( 'Request email confirmation', 'memberhero' ) . '">' . memberhero_svg_icon( 'mail' ) . '</a>';
		}

		if ( is_memberhero_pending_user( $user_id ) ) {
			if ( ! is_memberhero_rejected_user( $user_id ) ) {
				$actions[] = '<a href="' . wp_nonce_url( admin_url( 'admin-ajax.php?action=memberhero_approve_user&id=' . $user_id ), 'memberhero-approve-user' ) . '" class="button memberhero_ajax memberhero-top-tips" data-tip="' . esc_attr( 'Approve user', 'memberhero' ) . '">' . memberhero_svg_icon( 'check' ) . '</a>';
				$actions[] = '<a href="' . wp_nonce_url( admin_url( 'admin-ajax.php?action=memberhero_reject_user&id=' . $user_id ), 'memberhero-reject-user' ) . '" class="button memberhero_ajax memberhero-top-tips" data-tip="' . esc_attr( 'Reject user', 'memberhero' ) .'">' . memberhero_svg_icon( 'x' ) . '</a>';
			} else {
				$actions[] = '<a href="' . wp_nonce_url( admin_url( 'admin-ajax.php?action=memberhero_reinstate_user&id=' . $user_id ), 'memberhero-reinstate-user' ) . '" class="button memberhero_ajax memberhero-top-tips" data-tip="' . esc_attr( 'Reinstate user', 'memberhero' ) .'">' . memberhero_svg_icon( 'refresh-cw' ) . '</a>';
			}
		} else {
			$actions[] = '<a href="' . wp_nonce_url( admin_url( 'admin-ajax.php?action=memberhero_set_pending&id=' . $user_id ), 'memberhero-set-pending' ) . '" class="button memberhero_ajax memberhero-top-tips" data-tip="'. esc_attr( 'Set as pending', 'memberhero' ) . '">' . memberhero_svg_icon( 'clock' ) . '</a>';
		}

		$actions[] = '<a href="' . memberhero_get_profile_url( $user_id ) . '" class="button memberhero-top-tips" data-tip="' . esc_attr( 'View profile', 'memberhero' ) .'">' . memberhero_svg_icon( 'eye' ) . '</a>';
		$actions[] = '<a href="' . memberhero_get_edit_user_link( $user_id ) . '" class="button memberhero-top-tips" data-tip="' . esc_attr( 'Edit profile', 'memberhero' ) . '">' . memberhero_svg_icon( 'edit-2' ) . '</a>';

		return apply_filters( 'memberhero_users_admin_actions', $actions, $user_id );
	}

	/**
	 * Show status filter.
	 */
	public function show_status_filter( $which ) {
		global $wp_list_table;
		if ( $which != 'top' ) {
			return;
		}

		$selected = ! empty( $_GET[ 'memberhero_status' ] ) ? esc_attr( $_GET[ 'memberhero_status' ] ) : '';

		if ( ! $wp_list_table->has_items() ) {
			$select = '<select name="memberhero_status" class="memberhero-select memberhero-select-top nomargin">';
		} else {
			$select = '<select name="memberhero_status" class="memberhero-select memberhero-select-top">';
		}

		$select .= '<option value="">' . __( 'Filter by status...', 'memberhero' ). '</option>';

		foreach( memberhero_get_user_statuses() as $key => $value ) {
			$select .= '<option value="' . esc_attr( $key ) . '" ' . selected( $selected, $key, false ) . '>' . esc_html( $value ) . '</option>';
		}

		$select .= '</select>';

		echo $select;

		submit_button( __( 'Filter', 'memberhero' ), null, $which, false );
	}

	/**
	 * Filter users by a specific status.
	 */
	public function filter_by_status( $query ) {
		global $pagenow;
		if ( is_admin() && 'users.php' == $pagenow && ! empty( $_GET[ 'memberhero_status' ] ) ) {
			$status = esc_attr( $_GET[ 'memberhero_status' ] );
			switch( $status ) {
				case 'pending' :
					$meta_query = array(
						array(
							'key'     => '_memberhero_pending',
							'value'   => 1,
							'compare' => '=',
						),
						array(
							'key'     => '_memberhero_rejected',
							'compare' => 'NOT EXISTS',
						),
					);
				break;
				case 'rejected' :
					$meta_query = array(
						array(
							'key'     => '_memberhero_rejected',
							'value'   => 1,
							'compare' => '=',
						),
					);
				break;
				case 'unconfirmed' :
					$meta_query = array(
						array(
							'key'     => '_memberhero_unconfirmed_email',
							'value'   => 1,
							'compare' => '=',
						),
					);
				break;
				case 'approved' :
					$meta_query = array(
						array(
							'key'     => '_memberhero_pending',
							'compare' => 'NOT EXISTS',
						),
						array(
							'key'     => '_memberhero_rejected',
							'compare' => 'NOT EXISTS',
						),
						array(
							'key'     => '_memberhero_unconfirmed_email',
							'compare' => 'NOT EXISTS',
						),
					);
				break;
			}
			if ( isset( $meta_query ) ) {
				$query->set( 'meta_query', $meta_query );
			}
		}
	}

	/**
	 * Deletes user data when a user is deleted.
	 */
	function delete_user_data( $user_id ) {
		include_once MEMBERHERO_ABSPATH . 'includes/memberhero-user-hooks.php';

		memberhero_delete_user_files( $user_id );

		do_action( 'memberhero_user_deleted', $user_id );
	}

}

new MemberHero_Admin_Users();