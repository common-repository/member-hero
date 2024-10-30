<?php
/**
 * Load assets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Admin_Assets class.
 */
class MemberHero_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );
	}

	/**
	 * Enqueue styles.
	 */
	public function admin_styles() {
		global $wp_scripts;

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		// Register admin styles.
		wp_register_style( 'memberhero_admin_menu_styles', memberhero()->plugin_url() . '/assets/css/menu.css', array(), MEMBERHERO_VERSION );
		wp_register_style( 'memberhero_admin_styles', memberhero()->plugin_url() . '/assets/css/admin.css', array(), MEMBERHERO_VERSION );
		wp_register_style( 'memberhero_admin_dashboard_styles', memberhero()->plugin_url() . '/assets/css/dashboard.css', array(), MEMBERHERO_VERSION );
		wp_register_style( 'jquery-ui-css', memberhero()->plugin_url() . '/assets/css/jquery-ui-fresh.min', array(), MEMBERHERO_VERSION );

		// Add RTL support for admin styles.
		wp_style_add_data( 'memberhero_admin_menu_styles', 'rtl', 'replace' );
		wp_style_add_data( 'memberhero_admin_styles', 'rtl', 'replace' );
		wp_style_add_data( 'memberhero_admin_dashboard_styles', 'rtl', 'replace' );

		// Sitewide menu CSS.
		wp_enqueue_style( 'memberhero_admin_menu_styles' );

		// Admin styles for plugin pages only.
		if ( in_array( $screen_id, memberhero_get_screen_ids() ) ) {
			wp_enqueue_style( 'memberhero_admin_styles' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'jquery-ui-css' );
		}

		if ( in_array( $screen_id, array( 'dashboard' ) ) ) {
			wp_enqueue_style( 'memberhero_admin_dashboard_styles' );
		}

	}	

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		global $wp_query, $post;

		$screen       = get_current_screen();
		$screen_id    = $screen ? $screen->id : '';
		$memberhero_screen_id = sanitize_title( __( 'Member Hero', 'memberhero' ) );

		// Register scripts.
		wp_register_script( 'jquery-tiptip', memberhero()->plugin_url() . '/assets/js/jquery-tiptip/jquery-tiptip.js', array( 'jquery' ), MEMBERHERO_VERSION, true );
		wp_register_script( 'jquery-toggles', memberhero()->plugin_url() . '/assets/js/jquery-toggles/jquery-toggles.js', array( 'jquery' ), MEMBERHERO_VERSION, true );
		wp_register_script( 'jquery-modal', memberhero()->plugin_url() . '/assets/js/jquery-modal/jquery-modal.js', array( 'jquery' ), MEMBERHERO_VERSION, true );
		wp_register_script( 'jquery-selectize', memberhero()->plugin_url() . '/assets/js/jquery-selectize/jquery-selectize.js', array( 'jquery' ), MEMBERHERO_VERSION, true );
		wp_register_script( 'jquery-helper', memberhero()->plugin_url() . '/assets/js/jquery-helper/jquery-helper.js', array( 'jquery' ), MEMBERHERO_VERSION, true );
		wp_register_script( 'memberhero_admin', memberhero()->plugin_url() . '/assets/js/admin/memberhero_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'jquery-tiptip', 'jquery-toggles', 'jquery-modal', 'jquery-selectize', 'jquery-helper' ), MEMBERHERO_VERSION, true );
		wp_register_script( 'jquery-blockui', memberhero()->plugin_url() . '/assets/js/jquery-blockui/jquery-blockui.js', array( 'jquery' ), MEMBERHERO_VERSION, true );

		// Admin pages.
		if ( in_array( $screen_id, memberhero_get_screen_ids() ) ) {
			$dummy = new MemberHero_Field();

			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'memberhero_admin' );

			$params = array(
				'ajax_url'	=> admin_url( 'admin-ajax.php' ),
				'metakeys'	=> apply_filters( 'memberhero_field_meta_keys', $dummy->internal_meta_keys ),
				'yes'		=> __( 'yes', 'memberhero' ),
				'no'		=> __( 'no', 'memberhero' ),
				'svg'		=> memberhero()->plugin_url() . '/assets/images/feather-sprite.svg#',
				'nonces'	=> apply_filters( 'memberhero_admin_nonces', array(
					'save_form'				=> wp_create_nonce( 'memberhero-save-form' ),
					'create_forms' 			=> wp_create_nonce( 'memberhero-create-forms' ),
					'create_fields' 		=> wp_create_nonce( 'memberhero-create-fields' ),
					'create_roles'			=> wp_create_nonce( 'memberhero-create-roles' ),
					'create_lists'			=> wp_create_nonce( 'memberhero-create-lists' ),
					'add_field'				=> wp_create_nonce( 'memberhero-add-field' ),
					'json_search_users'		=> wp_create_nonce( 'memberhero-json-search-users' ),
					'json_search_terms'		=> wp_create_nonce( 'memberhero-json-search-terms' ),
					'ajaxnonce'				=> wp_create_nonce( 'memberhero-ajaxnonce' ),
				) ),
				'states'	=> array(
					'create_forms'			=> __( 'Creating forms...', 'memberhero' ),
					'create_fields'			=> __( 'Creating fields...', 'memberhero' ),
					'create_roles'			=> __( 'Creating roles...', 'memberhero' ),
					'create_lists'			=> __( 'Creating member directories...', 'memberhero' ),
					'done_redirect'			=> __( 'That&#39;s It! Please hold on.', 'memberhero' ),
					'show_less'				=> __( 'Show less', 'memberhero' ),
					'show_more'				=> __( 'Show more', 'memberhero' ),
					'ajax_error'			=> __( 'An error has occured.', 'memberhero' ),
					'duplicating'			=> __( 'Duplicating...', 'memberhero' ),
					'saving_changes'		=> __( 'Saving changes...', 'memberhero' ),
					'unsaved_changes'		=> __( 'Your changes will not take effect until you press on <b>Save changes &rarr;</b> button.', 'memberhero' ),
					'saved_changes'			=> __( 'Changes have been saved.', 'memberhero' ),
				),
				'modal'		=> array(
					'creating'				=> __( 'Create a Custom Field', 'memberhero' ),
					'editing'				=> __( 'Editing "{field}"', 'memberhero' ),
					'save_button'			=> __( 'Save Field &rarr;', 'memberhero' ),
					'create_button'			=> __( 'Create &rarr;', 'memberhero' ),
				),
			);

			wp_localize_script( 'memberhero_admin', 'memberhero_admin', $params );
		}

		// Form builder.
		if ( in_array( $screen_id, array( 'memberhero_form' ) ) ) {
			wp_register_script( 'memberhero-forms', memberhero()->plugin_url() . '/assets/js/admin/memberhero-forms.js', array( 'jquery' ), MEMBERHERO_VERSION, true );
			wp_enqueue_script( 'memberhero-forms' );
			wp_localize_script(
				'memberhero-forms',
				'memberhero_forms_params',
				array(

				)
			);
		}

		// Users admin.
		if ( in_array( $screen_id, array( 'users' ) ) ) {
			wp_register_script( 'memberhero-users', memberhero()->plugin_url() . '/assets/js/admin/users.js', array( 'jquery' ), MEMBERHERO_VERSION, true );
			wp_enqueue_script( 'memberhero-users' );
			wp_localize_script(
				'memberhero-users',
				'memberhero_users_params',
				array(

				)
			);
		}

		// API settings.
		if ( $memberhero_screen_id . '_page_memberhero-settings' === $screen_id && isset( $_GET['section'] ) && 'keys' == $_GET['section'] ) {
			wp_register_script( 'memberhero-api-keys', memberhero()->plugin_url() . '/assets/js/admin/api-keys.js', array( 'jquery', 'memberhero_admin', 'underscore', 'backbone', 'wp-util' ), MEMBERHERO_VERSION, true );
			wp_enqueue_script( 'memberhero-api-keys' );
			wp_localize_script(
				'memberhero-api-keys',
				'memberhero_admin_api_keys',
				array(
					'ajax_url'         => admin_url( 'admin-ajax.php' ),
					'update_api_nonce' => wp_create_nonce( 'update-api-key' ),
				)
			);
		}

		// Add js for regular posts.
		if ( in_array( $screen_id, array( 'post', 'page', 'edit-category' ) ) ) {
			wp_register_script( 'memberhero-post', memberhero()->plugin_url() . '/assets/js/admin/post.js', array( 'jquery' ), MEMBERHERO_VERSION, true );
			wp_enqueue_script( 'memberhero-post' );
			wp_localize_script(
				'memberhero-post',
				'memberhero_post',
				array(
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'ajaxnonce' 	=> wp_create_nonce( 'memberhero-ajaxnonce' ),
				)
			);
		}
	}

}

return new MemberHero_Admin_Assets();