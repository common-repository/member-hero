<?php
/**
 * List tables: forms.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MemberHero_Admin_List_Table', false ) ) {
	include_once 'abstract-class-memberhero-admin-list-table.php';
}

/**
 * MemberHero_Admin_List_Table_Forms class.
 */
class MemberHero_Admin_List_Table_Forms extends MemberHero_Admin_List_Table {

	/**
	 * Post type.
	 */
	protected $list_table_type = 'memberhero_form';

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
		echo '<div class="memberhero-BlankState">' . memberhero_svg_icon( 'file-text' );
		echo '<h2 class="memberhero-BlankState-message">' . esc_html__( 'Create registration, login and profile forms using a robust and easy-to-use drag and drop form builder.', 'memberhero' ) . '</h2>';
		echo '<a class="memberhero-BlankState-cta button-primary button" href="' . esc_url( admin_url( 'post-new.php?post_type=memberhero_form' ) ) . '">' . esc_html__( 'Create your first form', 'memberhero' ) . '</a>';
		echo '<a class="memberhero-BlankState-cta button" target="_blank" href="">' . esc_html__( 'Learn more about forms', 'memberhero' ) . '</a>';
		echo '</div>';
		if ( current_user_can( 'publish_memberhero_forms' ) ) {
			echo '<a href="#memberhero-create-forms" class="memberhero-page-title-action button button-primary">' . esc_html__( 'Create default forms', 'memberhero' ) . '</a>';
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
		if ( empty( $actions ) ) {
			$actions = array();
		}
		$actions = array_merge( array( 'id' => sprintf( esc_html__( 'ID: %d', 'memberhero' ), $post->ID ) ), $actions );
		if ( $post->post_status == 'publish' && current_user_can( 'publish_memberhero_forms' ) ) {
			$actions = memberhero_array_insert_after( 'inline hide-if-no-js', $actions, 'duplicate', sprintf( '<a href="' . admin_url( 'post.php?post=' . $post->ID . '&amp;action=duplicate&amp;_wpnonce=' . wp_create_nonce( 'duplicate-form' ) ) . '" class="duplicate_form" aria-label="%s">%s</a>', esc_html__( 'Duplicate this form', 'memberhero' ), esc_html__( 'Duplicate', 'memberhero' ) ) );
		}
		return $actions;
	}

	/**
	 * Define which columns are sortable.
	 */
	public function define_sortable_columns( $columns ) {
		$custom = array(
			'name'  	=> 'title',
			'type'		=> 'type',
			'memberhero_date'	=> 'date',
		);
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Define which columns to show on this screen.
	 */
	public function define_columns( $columns ) {
		$show_columns 				= array();
		$show_columns['cb']			= $columns['cb'];
		$show_columns['memberhero_status'] = '';
		$show_columns['name']  		= esc_html__( 'Name', 'memberhero' );
		$show_columns['type']  		= esc_html__( 'Type', 'memberhero' );
		$show_columns['shortcode']	= esc_html__( 'Shortcode', 'memberhero' );
		$show_columns['memberhero_date']  	= esc_html__( 'Date Added', 'memberhero' );

		return $show_columns;
	}

	/**
	 * Pre-fetch any data for the row each column has access to it. global is there for bw compat.
	 */
	protected function prepare_row_data( $post_id ) {
		global $the_form;

		if ( empty( $this->object ) || $this->object->id !== $post_id ) {
			$this->object = new MemberHero_Form( $post_id );
			$the_form = $this->object;
		}
	}

	/**
	 * Render column: memberhero_status.
	 */
	protected function render_memberhero_status_column() {
		global $post;

		if ( is_memberhero_default_form( $this->object ) ) {
			echo '<span class="status-active tips" data-tip="' . esc_attr__( 'Default', 'memberhero' ) . '">' . memberhero_svg_icon( 'check' ) . '</span>';
		} else {
			echo '<span class="status-manual tips" data-tip="' . esc_attr__( 'Manual', 'memberhero' ) . '">' . memberhero_svg_icon( 'arrow-right' ) . '</span>';
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

		// Add the role label if the form is specific to a role.
		if ( $this->object->get_role_title() && $this->object->force_role === 'yes' ) {
			echo '<span class="memberhero-tag">' . $this->object->get_role_title() . '</span>';
		}

		get_inline_data( $post );

		/* Custom inline data. */
		echo '
			<div class="hidden" id="memberhero_inline_' . absint( $this->object->id ) . '">
			</div>
		';
	}

	/**
	 * Render columm: type.
	 */
	protected function render_type_column() {
		if ( $this->object->type ) {
			echo memberhero_get_form_type( $this->object->type );
			if ( $this->object->endpoint && in_array( $this->object->type, array( 'account' ) ) ) {
				$endpoint = memberhero()->query->get_endpoint_title( $this->object->endpoint );
				$endpoint = ! empty( $endpoint ) ? $endpoint : esc_html__( 'Unassigned', 'memberhero' );
				echo '/ ' . $endpoint;
			}
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

		if ( 'edit.php' !== $pagenow || 'memberhero_form' !== $typenow || ! get_query_var( 'form_search' ) || ! isset( $_GET['s'] ) ) {
			return $query;
		}

		return memberhero_clean( wp_unslash( $_GET['s'] ) );
	}

	/**
	 * Render any custom filters and search inputs for the list table.
	 */
	protected function render_filters() {
		?>
		<select name="form_type" id="dropdown_form_type_type">
			<option value=""><?php esc_html_e( 'Show all types', 'memberhero' ); ?></option>
			<?php
			$types = memberhero_get_form_types();

			foreach ( $types as $name => $type ) {
				echo '<option value="' . esc_attr( $name ) . '"';

				if ( isset( $_GET['form_type'] ) ) {
					selected( $name, memberhero_clean( wp_unslash( $_GET['form_type'] ) ) );
				}

				echo '>' . esc_html( $type['label'] ) . '</option>';
			}
			?>
		</select>
		<?php
	}

	/**
	 * Handle any query filters.
	 */
	protected function query_filters( $query_vars ) {

		if ( ! empty( $_GET['form_type'] ) ) {
			$query_vars['meta_key']   = 'type';
			$query_vars['meta_value'] = memberhero_clean( wp_unslash( $_GET['form_type'] ) );
		}

		if ( isset( $query_vars['orderby'] ) ) {
			if ( 'type' === $query_vars['orderby'] ) {
				$query_vars = array_merge(
					$query_vars,
					array(
						'meta_key' => 'type',
						'orderby'  => 'meta_value',
					)
				);
			}
		}

		// Search.
		if ( ! empty( $query_vars['s'] ) ) {
			$data_store							= MemberHero_Data_Store::load( 'form' );
			$ids								= $data_store->search_forms( memberhero_clean( wp_unslash( $_GET['s'] ) ) );
			$query_vars['post__in']       		= array_merge( $ids, array( 0 ) );
			$query_vars['form_search'] 			= true;
			unset( $query_vars['s'] );
		}

		return $query_vars;
	}

}