<?php
/**
 * List tables: user roles.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MemberHero_Admin_List_Table', false ) ) {
	include_once 'abstract-class-memberhero-admin-list-table.php';
}

/**
 * MemberHero_Admin_List_Table_Lists class.
 */
class MemberHero_Admin_List_Table_Lists extends MemberHero_Admin_List_Table {

	/**
	 * Post type.
	 */
	protected $list_table_type = 'memberhero_list';

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'disable_months_dropdown', '__return_true' );
		add_filter( 'get_search_query', array( $this, 'search_label' ) );
	}

	/**
	 * Render blank state.
	 */
	protected function render_blank_state() {
		echo '<div class="memberhero-BlankState">' . memberhero_svg_icon( 'users' );
		echo '<h2 class="memberhero-BlankState-message">' . esc_html__( 'Create unlimited member directories using any criteria and allow community members to search and filter results.', 'memberhero' ) . '</h2>';
		echo '<a class="memberhero-BlankState-cta button-primary button" href="' . esc_url( admin_url( 'post-new.php?post_type=memberhero_list' ) ) . '">' . esc_html__( 'Create a new member directory', 'memberhero' ) . '</a>';
		echo '<a class="memberhero-BlankState-cta button" target="_blank" href="">' . esc_html__( 'Learn more about member directories', 'memberhero' ) . '</a>';
		echo '</div>';
		if ( current_user_can( 'publish_memberhero_lists' ) ) {
			echo '<a href="#memberhero-create-lists" class="memberhero-page-title-action button button-primary">' . esc_html__( 'Create default member directories', 'memberhero' ) . '</a>';
		}
	}

	/**
	 * Define primary column.
	 */
	protected function get_primary_column() {
		return 'name';
	}

	/**
	 * Get row actions to show in the list table.
	 */
	protected function get_row_actions( $actions, $post ) {
		$actions = array_merge( array( 'id' => sprintf( esc_html__( 'ID: %d', 'memberhero' ), $post->ID ) ), $actions );
		if ( $post->post_status == 'publish' ) {
			$actions = memberhero_array_insert_after( 'inline hide-if-no-js', $actions, 'duplicate', sprintf( '<a href="' . admin_url( 'post.php?post=' . $post->ID . '&amp;action=duplicate&amp;_wpnonce=' . wp_create_nonce( 'duplicate-list' ) ) . '" class="duplicate_list" aria-label="%s">%s</a>', esc_html__( 'Duplicate this member directory', 'memberhero' ), esc_html__( 'Duplicate', 'memberhero' ) ) );
		}
		return $actions;
	}

	/**
	 * Define which columns are sortable.
	 */
	public function define_sortable_columns( $columns ) {
		$custom = array(
			'name'  	=> 'title',
			'memberhero_date'	=> 'date',
		);
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Define which columns to show on this screen.
	 */
	public function define_columns( $columns ) {
		$show_columns               = array();
		$show_columns['cb']         = $columns['cb'];
		$show_columns['name']  		= esc_html__( 'Name', 'memberhero' );
		$show_columns['show_users'] = esc_html__( 'Who to show in this directory?', 'memberhero' );
		$show_columns['shortcode']	= esc_html__( 'Shortcode', 'memberhero' );
		$show_columns['memberhero_date']  	= esc_html__( 'Date Added', 'memberhero' );

		return $show_columns;
	}

	/**
	 * Pre-fetch any data for the row each column has access to it. global is there for bw compat.
	 */
	protected function prepare_row_data( $post_id ) {
		global $the_list;

		if ( empty( $this->object ) || $this->object->id !== $post_id ) {
			$this->object = new MemberHero_List( $post_id );
			$the_list = $this->object;
		}
	}

	/**
	 * Render column: name.
	 */
	protected function render_name_column() {
		global $post;

		$edit_link = get_edit_post_link( $this->object->id );
		$title     = _draft_or_post_title();

		echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';
		_post_states( $post );
		echo '</strong>';

		get_inline_data( $post );

		/* Custom inline data. */
		echo '
			<div class="hidden" id="memberhero_inline_' . absint( $this->object->id ) . '">
			</div>
		';
	}

	/**
	 * Render columm: show_users.
	 */
	protected function render_show_users_column() {
		if ( $this->object->roles ) {
			$roles = array_merge( array( '_all' => esc_html__( 'Everyone excluding admins', 'memberhero' ) ), memberhero_get_roles() );
			foreach( $this->object->roles as $value ) {
				echo '<span class="memberhero-tag">' . esc_html__( $roles[ $value ] ) . '</span>';
			}
		} else {
			echo '<span class="memberhero-tag">' . esc_html__( 'Everyone', 'memberhero' ) . '</span>';
		}
	}

	/**
	 * Render columm: shortcode.
	 */
	protected function render_shortcode_column() {
		echo $this->object->get_shortcode();
	}

	/**
	 * Render columm: date.
	 */
	protected function render_memberhero_date_column() {
		$date = sprintf( esc_html__( '%1$s at %2$s', 'memberhero' ), date_i18n( memberhero_date_format(), strtotime( get_the_time() ) ), date_i18n( memberhero_time_format(), strtotime( get_the_time() ) ) );

		echo $date;
	}

	/**
	 * Change the label when searching
	 */
	public function search_label( $query ) {
		global $pagenow, $typenow;

		if ( 'edit.php' !== $pagenow || 'memberhero_list' !== $typenow || ! get_query_var( 'list_search' ) || ! isset( $_GET['s'] ) ) {
			return $query;
		}

		return memberhero_clean( wp_unslash( $_GET['s'] ) );
	}

	/**
	 * Handle any query filters.
	 */
	protected function query_filters( $query_vars ) {

		if ( isset( $query_vars['orderby'] ) ) {
		}

		// Search.
		if ( ! empty( $query_vars['s'] ) ) {
			$data_store							= MemberHero_Data_Store::load( 'list' );
			$ids								= $data_store->search_lists( memberhero_clean( wp_unslash( $_GET['s'] ) ) );
			$query_vars['post__in']       		= array_merge( $ids, array( 0 ) );
			$query_vars['list_search'] 			= true;
			unset( $query_vars['s'] );
		}

		return $query_vars;
	}

}