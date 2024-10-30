<?php
/**
 * List tables: custom fields.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MemberHero_Admin_List_Table', false ) ) {
	include_once 'abstract-class-memberhero-admin-list-table.php';
}

/**
 * MemberHero_Admin_List_Table_Fields class.
 */
class MemberHero_Admin_List_Table_Fields extends MemberHero_Admin_List_Table {

	/**
	 * Post type.
	 */
	protected $list_table_type = 'memberhero_field';

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
		echo '<div class="memberhero-BlankState">' . memberhero_svg_icon( 'database' );
		echo '<h2 class="memberhero-BlankState-message">' . esc_html__( 'Create unlimited custom fields to fully customize the content of login, registration and profile forms.', 'memberhero' ) . '</h2>';
		echo '<a class="memberhero-BlankState-cta button-primary button" href="' . esc_url( admin_url( 'post-new.php?post_type=memberhero_field' ) ) . '">' . esc_html__( 'Create a custom field', 'memberhero' ) . '</a>';
		echo '<a class="memberhero-BlankState-cta button" target="_blank" href="">' . esc_html__( 'Learn more about custom fields', 'memberhero' ) . '</a>';
		echo '</div>';
		if ( current_user_can( 'publish_memberhero_fields' ) ) {
			echo '<a href="#memberhero-create-fields" class="memberhero-page-title-action button button-primary">' . esc_html__( 'Create default custom fields', 'memberhero' ) . '</a>';
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
			$actions = memberhero_array_insert_after( 'inline hide-if-no-js', $actions, 'duplicate', sprintf( '<a href="' . admin_url( 'post.php?post=' . $post->ID . '&amp;action=duplicate&amp;_wpnonce=' . wp_create_nonce( 'duplicate-field' ) ) . '" class="duplicate_field" aria-label="%s">%s</a>', esc_html__( 'Duplicate this field', 'memberhero' ), esc_html__( 'Duplicate', 'memberhero' ) ) );
		}
		return $actions;
	}

	/**
	 * Define which columns are sortable.
	 */
	public function define_sortable_columns( $columns ) {
		$custom = array(
			'name'  			=> 'title',
			'key'				=> 'key',
			'type'				=> 'type',
			'memberhero_date'	=> 'date',
		);
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Define which columns to show on this screen.
	 */
	public function define_columns( $columns ) {
		$show_columns               		= array();
		$show_columns['cb']        		 	= $columns['cb'];
		$show_columns['name']  				= __( 'Name', 'memberhero' );
		$show_columns['key']  				= __( 'Label', 'memberhero' );
		$show_columns['type']  				= __( 'Type', 'memberhero' );
		$show_columns['options']  			= __( 'Options', 'memberhero' );
		$show_columns['can_view']  			= __( 'Who can view?', 'memberhero' );
		$show_columns['memberhero_date']  	= __( 'Date Added', 'memberhero' );

		return apply_filters( 'manage_memberhero_field_columns', $show_columns );
	}

	/**
	 * Pre-fetch any data for the row each column has access to it. global is there for bw compat.
	 */
	protected function prepare_row_data( $post_id ) {
		global $the_field;

		if ( empty( $this->object ) || $this->object->id !== $post_id ) {
			$this->object = new MemberHero_Field( $post_id );
			$the_field = $this->object;
		}
	}

	/**
	 * Render column: name.
	 */
	protected function render_name_column() {
		global $post;

		$edit_link = get_edit_post_link( $this->object->id );
		$title     = _draft_or_post_title();

		echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html__( $title, 'memberhero' ) . '</a>';
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
	 * Render columm: key.
	 */
	protected function render_key_column() {
		if ( $this->object->key ) {
			echo '<span class="memberhero-tag">' . esc_html__( $this->object->key ) . '</span>';
		}
	}

	/**
	 * Render columm: type.
	 */
	protected function render_type_column() {
		if ( $this->object->type ) {
			echo memberhero_get_field_type( $this->object->type, 'html' );
		}
	}

	/**
	 * Render columm: options.
	 */
	protected function render_options_column() {

		// Required
		if ( $this->object->is_required ) {
			echo '<span class="memberhero-icon yes tips" data-tip="' . esc_html__( 'Required', 'memberhero' ) . '">' . memberhero_svg_icon( 'star' ) . '</span>';
		} else {
			echo '<span class="memberhero-icon no tips" data-tip="' . esc_html__( 'Optional', 'memberhero' ) . '">' . memberhero_svg_icon( 'star' ) . '</span>';
		}

		// Private
		if ( $this->object->is_private ) {
			echo '<span class="memberhero-icon yes tips" data-tip="' . esc_html__( 'Private', 'memberhero' ) . '">' . memberhero_svg_icon( 'shield' ) . '</span>';
		} else {
			echo '<span class="memberhero-icon no tips" data-tip="' . esc_html__( 'Public', 'memberhero' ) . '">' . memberhero_svg_icon( 'shield-off' ) . '</span>';
		}

		// Read only
		if ( $this->object->is_readonly ) {
			echo '<span class="memberhero-icon yes tips" data-tip="' . esc_html__( 'Read Only', 'memberhero' ) . '">' . memberhero_svg_icon( 'lock' ) . '</span>';
		} else {
			echo '<span class="memberhero-icon no tips" data-tip="' . esc_html__( 'Editable', 'memberhero' ) . '">' . memberhero_svg_icon( 'lock' ) . '</span>';
		}

	}

	/**
	 * Render columm: can_view.
	 */
	protected function render_can_view_column() {
		if ( $this->object->can_view ) {
			$can_view = array_merge( array( '_none' => esc_html__( 'No one', 'memberhero' ), 'owner' => esc_html__( 'Owner', 'memberhero' ) ), memberhero_get_roles() );
			foreach( $this->object->can_view as $value ) {
				echo '<span class="memberhero-tag">' . esc_html__( $can_view[ $value ] ) . '</span>';
			}
		} else {
			echo '<span class="memberhero-tag">' . esc_html__( 'Everyone', 'memberhero' ) . '</span>';
		}
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

		if ( 'edit.php' !== $pagenow || 'memberhero_field' !== $typenow || ! get_query_var( 'field_search' ) || ! isset( $_GET['s'] ) ) {
			return $query;
		}

		return memberhero_clean( wp_unslash( $_GET['s'] ) );
	}

	/**
	 * Render any custom filters and search inputs for the list table.
	 */
	protected function render_filters() {
		?>
		<select name="field_type" id="dropdown_custom_field_type">
			<option value=""><?php esc_html_e( 'Show all types', 'memberhero' ); ?></option>
			<?php
			$types = memberhero_get_field_types();

			foreach ( $types as $name => $type ) {
				echo '<option value="' . esc_attr( $name ) . '"';

				if ( isset( $_GET['field_type'] ) ) {
					selected( $name, memberhero_clean( wp_unslash( $_GET['field_type'] ) ) );
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

		if ( ! empty( $_GET['field_type'] ) ) {
			$query_vars['meta_key']   = 'type';
			$query_vars['meta_value'] = memberhero_clean( wp_unslash( $_GET['field_type'] ) );
		}

		if ( isset( $query_vars['orderby'] ) ) {
			if ( 'key' === $query_vars['orderby'] ) {
				$query_vars = array_merge(
					$query_vars,
					array(
						'meta_key' => 'key',
						'orderby'  => 'meta_value',
					)
				);
			}
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
			$data_store							= MemberHero_Data_Store::load( 'field' );
			$ids								= $data_store->search_fields( memberhero_clean( wp_unslash( $_GET['s'] ) ) );
			$query_vars['post__in']       		= array_merge( $ids, array( 0 ) );
			$query_vars['field_search'] 		= true;
			unset( $query_vars['s'] );
		}

		return $query_vars;
	}

}