<?php
/**
 * Post Types Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Admin_Post_Types class.
 */
class MemberHero_Admin_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		include_once dirname( __FILE__ ) . '/class-memberhero-admin-meta-boxes.php';

		// Load correct list table classes for current screen.
		add_action( 'current_screen', array( $this, 'setup_screen' ) );
		add_action( 'check_ajax_referer', array( $this, 'setup_screen' ) );

		// Admin notices.
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		// Extra post data and screen elements.
		add_action( 'edit_form_top', array( $this, 'edit_form_top' ) );
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );

		// Fire when specific post type is deleted.
		add_action( 'before_delete_post', array( $this, 'before_delete_post' ) );
	}

	/**
	 * Looks at the current screen and loads the correct list table handler.
	 */
	public function setup_screen() {
		global $memberhero_list_table;

		$screen_id = false;

		if ( function_exists( 'get_current_screen' ) ) {
			$screen    = get_current_screen();
			$screen_id = isset( $screen, $screen->id ) ? $screen->id : '';
		}

		if ( ! empty( $_REQUEST['screen'] ) ) {
			$screen_id = memberhero_clean( wp_unslash( $_REQUEST['screen'] ) );
		}

		switch ( $screen_id ) {
			case 'edit-memberhero_form':
				include_once 'list-tables/class-memberhero-admin-list-table-forms.php';
				$memberhero_list_table = new MemberHero_Admin_List_Table_Forms();
				break;
			case 'edit-memberhero_field':
				include_once 'list-tables/class-memberhero-admin-list-table-fields.php';
				$memberhero_list_table = new MemberHero_Admin_List_Table_Fields();
				break;
			case 'edit-memberhero_role':
				include_once 'list-tables/class-memberhero-admin-list-table-roles.php';
				$memberhero_list_table = new MemberHero_Admin_List_Table_Roles();
				break;
			case 'edit-memberhero_list':
				include_once 'list-tables/class-memberhero-admin-list-table-lists.php';
				$memberhero_list_table = new MemberHero_Admin_List_Table_Lists();
				break;
		}

		// Ensure the table handler is only loaded once. Prevents multiple loads if a plugin calls check_ajax_referer many times.
		remove_action( 'current_screen', array( $this, 'setup_screen' ) );
		remove_action( 'check_ajax_referer', array( $this, 'setup_screen' ) );
	}

	/**
	 * Change messages when a post type is updated.
	 */
	public function post_updated_messages( $messages ) {
		global $post;

		$messages['memberhero_form'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Form updated.', 'memberhero' ),
			2  => __( 'Custom field updated.', 'memberhero' ),
			3  => __( 'Custom field deleted.', 'memberhero' ),
			4  => __( 'Form updated.', 'memberhero' ),
			5  => __( 'Revision restored.', 'memberhero' ),
			6  => __( 'Form updated.', 'memberhero' ),
			7  => __( 'Form saved.', 'memberhero' ),
			8  => __( 'Form submitted.', 'memberhero' ),
			9  => sprintf(
				__( 'Form scheduled for: %s.', 'memberhero' ),
				'<strong>' . date_i18n( __( 'M j, Y @ G:i', 'memberhero' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			10 => __( 'Form draft updated.', 'memberhero' ),
			11 => __( 'Form updated and sent.', 'memberhero' ),
		);

		$messages['memberhero_field'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Custom field updated.', 'memberhero' ),
			2  => __( 'Custom field updated.', 'memberhero' ),
			3  => __( 'Custom field deleted.', 'memberhero' ),
			4  => __( 'Custom field updated.', 'memberhero' ),
			5  => __( 'Revision restored.', 'memberhero' ),
			6  => __( 'Custom field updated.', 'memberhero' ),
			7  => __( 'Custom field saved.', 'memberhero' ),
			8  => __( 'Custom field submitted.', 'memberhero' ),
			9  => sprintf(
				__( 'Custom field scheduled for: %s.', 'memberhero' ),
				'<strong>' . date_i18n( __( 'M j, Y @ G:i', 'memberhero' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			10 => __( 'Custom field draft updated.', 'memberhero' ),
			11 => __( 'Custom field updated and sent.', 'memberhero' ),
		);

		$messages['memberhero_role'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'User role updated.', 'memberhero' ),
			2  => __( 'Custom field updated.', 'memberhero' ),
			3  => __( 'Custom field deleted.', 'memberhero' ),
			4  => __( 'User role updated.', 'memberhero' ),
			5  => __( 'Revision restored.', 'memberhero' ),
			6  => __( 'User role updated.', 'memberhero' ),
			7  => __( 'User role saved.', 'memberhero' ),
			8  => __( 'User role submitted.', 'memberhero' ),
			9  => sprintf(
				__( 'User role scheduled for: %s.', 'memberhero' ),
				'<strong>' . date_i18n( __( 'M j, Y @ G:i', 'memberhero' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			10 => __( 'User role draft updated.', 'memberhero' ),
			11 => __( 'User role updated and sent.', 'memberhero' ),
		);

		$messages['memberhero_list'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Member list updated.', 'memberhero' ),
			2  => __( 'Custom field updated.', 'memberhero' ),
			3  => __( 'Custom field deleted.', 'memberhero' ),
			4  => __( 'Member list updated.', 'memberhero' ),
			5  => __( 'Revision restored.', 'memberhero' ),
			6  => __( 'Member list updated.', 'memberhero' ),
			7  => __( 'Member list saved.', 'memberhero' ),
			8  => __( 'Member list submitted.', 'memberhero' ),
			9  => sprintf(
				__( 'Member list scheduled for: %s.', 'memberhero' ),
				'<strong>' . date_i18n( __( 'M j, Y @ G:i', 'memberhero' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			10 => __( 'Member list draft updated.', 'memberhero' ),
			11 => __( 'Member list updated and sent.', 'memberhero' ),
		);

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages['memberhero_form'] = array(
			'updated'   => _n( '%s form updated.', '%s forms updated.', $bulk_counts['updated'], 'memberhero' ),
			'locked'    => _n( '%s form not updated, somebody is editing it.', '%s forms not updated, somebody is editing them.', $bulk_counts['locked'], 'memberhero' ),
			'deleted'   => _n( '%s form permanently deleted.', '%s forms permanently deleted.', $bulk_counts['deleted'], 'memberhero' ),
			'trashed'   => _n( '%s form moved to the Trash.', '%s forms moved to the Trash.', $bulk_counts['trashed'], 'memberhero' ),
			'untrashed' => _n( '%s form restored from the Trash.', '%s forms restored from the Trash.', $bulk_counts['untrashed'], 'memberhero' ),
		);

		$bulk_messages['memberhero_field'] = array(
			'updated'   => _n( '%s custom field updated.', '%s custom fields updated.', $bulk_counts['updated'], 'memberhero' ),
			'locked'    => _n( '%s custom field not updated, somebody is editing it.', '%s custom fields not updated, somebody is editing them.', $bulk_counts['locked'], 'memberhero' ),
			'deleted'   => _n( '%s custom field permanently deleted.', '%s custom fields permanently deleted.', $bulk_counts['deleted'], 'memberhero' ),
			'trashed'   => _n( '%s custom field moved to the Trash.', '%s custom fields moved to the Trash.', $bulk_counts['trashed'], 'memberhero' ),
			'untrashed' => _n( '%s custom field restored from the Trash.', '%s custom fields restored from the Trash.', $bulk_counts['untrashed'], 'memberhero' ),
		);

		$bulk_messages['memberhero_role'] = array(
			'updated'   => _n( '%s user role updated.', '%s user roles updated.', $bulk_counts['updated'], 'memberhero' ),
			'locked'    => _n( '%s user role not updated, somebody is editing it.', '%s user roles not updated, somebody is editing them.', $bulk_counts['locked'], 'memberhero' ),
			'deleted'   => _n( '%s user role permanently deleted.', '%s user roles permanently deleted.', $bulk_counts['deleted'], 'memberhero' ),
			'trashed'   => _n( '%s user role moved to the Trash.', '%s user roles moved to the Trash.', $bulk_counts['trashed'], 'memberhero' ),
			'untrashed' => _n( '%s user role restored from the Trash.', '%s user roles restored from the Trash.', $bulk_counts['untrashed'], 'memberhero' ),
		);

		$bulk_messages['memberhero_list'] = array(
			'updated'   => _n( '%s member directory updated.', '%s member directories updated.', $bulk_counts['updated'], 'memberhero' ),
			'locked'    => _n( '%s member directory not updated, somebody is editing it.', '%s member directories not updated, somebody is editing them.', $bulk_counts['locked'], 'memberhero' ),
			'deleted'   => _n( '%s member directory permanently deleted.', '%s member directories permanently deleted.', $bulk_counts['deleted'], 'memberhero' ),
			'trashed'   => _n( '%s member directory moved to the Trash.', '%s member directories moved to the Trash.', $bulk_counts['trashed'], 'memberhero' ),
			'untrashed' => _n( '%s member directory restored from the Trash.', '%s member directories restored from the Trash.', $bulk_counts['untrashed'], 'memberhero' ),
		);

		return $bulk_messages;
	}

	/**
	 * Output extra data on post forms.
	 */
	public function edit_form_top( $post ) {
		echo '<input type="hidden" id="original_post_title" name="original_post_title" value="' . esc_attr( $post->post_title ) . '" />';
	}

	/**
	 * Change title boxes in admin.
	 */
	public function enter_title_here( $text, $post ) {
		switch ( $post->post_type ) {
			case 'memberhero_form':
				$text = __( 'e.g. Registration', 'memberhero' );
				break;
			case 'memberhero_field':
				$text = __( 'e.g. Location', 'memberhero' );
				break;
			case 'memberhero_role':
				$text = __( 'e.g. Premium member', 'memberhero' );
				break;
			case 'memberhero_list':
				$text = __( 'e.g. Member Directory', 'memberhero' );
				break;
		}
		return $text;
	}

	/**
	 * Before a post is completely removed.
	 */
	public function before_delete_post( $post_id ) {
		global $post_type;
		if ( in_array( $post_type, memberhero_get_post_types() ) ) {
			$object = new $post_type( $post_id );
			$object->delete();
		}
	}

}

new MemberHero_Admin_Post_Types();