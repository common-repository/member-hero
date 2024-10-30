<?php
/**
 * Registers post types and taxonomies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Post_Types class.
 */
class MemberHero_Post_Types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'memberhero_after_register_post_type', array( __CLASS__, 'maybe_flush_rewrite_rules' ) );
		add_action( 'memberhero_flush_rewrite_rules', array( __CLASS__, 'flush_rewrite_rules' ) );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( 'memberhero_form' ) ) {
			return;
		}

		do_action( 'memberhero_register_post_types' );

		register_post_type(
			'memberhero_form',
			apply_filters(
				'memberhero_register_post_type_form',
				array(
					'labels'             => array(
						'name'                  => __( 'Forms', 'memberhero' ),
						'singular_name'         => __( 'Form', 'memberhero' ),
						'menu_name'             => esc_html_x( 'Forms', 'Admin menu name', 'memberhero' ),
						'add_new'               => __( 'Add form', 'memberhero' ),
						'add_new_item'          => __( 'Add new form', 'memberhero' ),
						'edit'                  => __( 'Edit', 'memberhero' ),
						'edit_item'             => __( 'Edit form', 'memberhero' ),
						'new_item'              => __( 'New form', 'memberhero' ),
						'view_item'             => __( 'View form', 'memberhero' ),
						'search_items'          => __( 'Search forms', 'memberhero' ),
						'not_found'             => __( 'No forms found', 'memberhero' ),
						'not_found_in_trash'    => __( 'No forms found in trash', 'memberhero' ),
						'parent'                => __( 'Parent form', 'memberhero' ),
						'filter_items_list'     => __( 'Filter forms', 'memberhero' ),
						'items_list_navigation' => __( 'Forms navigation', 'memberhero' ),
						'items_list'            => __( 'Forms list', 'memberhero' ),
					),
					'description'         => __( 'This is where you can add new forms to customize your community pages.', 'memberhero' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'memberhero_form',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_memberhero' ) ? 'memberhero' : true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title' ),
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => true,
				)
			)
		);

		register_post_type(
			'memberhero_field',
			apply_filters(
				'memberhero_register_post_type_field',
				array(
					'labels'             => array(
						'name'                  => __( 'Custom fields', 'memberhero' ),
						'singular_name'         => __( 'Custom field', 'memberhero' ),
						'menu_name'             => esc_html_x( 'Custom fields', 'Admin menu name', 'memberhero' ),
						'add_new'               => __( 'Add custom field', 'memberhero' ),
						'add_new_item'          => __( 'Add new custom field', 'memberhero' ),
						'edit'                  => __( 'Edit', 'memberhero' ),
						'edit_item'             => __( 'Edit custom field', 'memberhero' ),
						'new_item'              => __( 'New custom field', 'memberhero' ),
						'view_item'             => __( 'View custom field', 'memberhero' ),
						'search_items'          => __( 'Search custom fields', 'memberhero' ),
						'not_found'             => __( 'No custom fields found', 'memberhero' ),
						'not_found_in_trash'    => __( 'No custom fields found in trash', 'memberhero' ),
						'parent'                => __( 'Parent custom field', 'memberhero' ),
						'filter_items_list'     => __( 'Filter custom fields', 'memberhero' ),
						'items_list_navigation' => __( 'Custom fields navigation', 'memberhero' ),
						'items_list'            => __( 'Custom fields list', 'memberhero' ),
					),
					'description'         => __( 'This is where you can manage custom fields.', 'memberhero' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'memberhero_field',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_memberhero' ) ? 'memberhero' : true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title' ),
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => true,
				)
			)
		);

		register_post_type(
			'memberhero_role',
			apply_filters(
				'memberhero_register_post_type_role',
				array(
					'labels'             => array(
						'name'                  => __( 'User roles', 'memberhero' ),
						'singular_name'         => __( 'User role', 'memberhero' ),
						'menu_name'             => esc_html_x( 'User roles', 'Admin menu name', 'memberhero' ),
						'add_new'               => __( 'Add user role', 'memberhero' ),
						'add_new_item'          => __( 'Add new user role', 'memberhero' ),
						'edit'                  => __( 'Edit', 'memberhero' ),
						'edit_item'             => __( 'Edit user role', 'memberhero' ),
						'new_item'              => __( 'New user role', 'memberhero' ),
						'view_item'             => __( 'View user role', 'memberhero' ),
						'search_items'          => __( 'Search user roles', 'memberhero' ),
						'not_found'             => __( 'No user roles found', 'memberhero' ),
						'not_found_in_trash'    => __( 'No user roles found in trash', 'memberhero' ),
						'parent'                => __( 'Parent user role', 'memberhero' ),
						'filter_items_list'     => __( 'Filter user roles', 'memberhero' ),
						'items_list_navigation' => __( 'User roles navigation', 'memberhero' ),
						'items_list'            => __( 'User roles list', 'memberhero' ),
					),
					'description'         => __( 'This is where you can add and manage your community roles.', 'memberhero' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'memberhero_role',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_memberhero' ) ? 'memberhero' : true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title' ),
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => true,
				)
			)
		);

		register_post_type(
			'memberhero_list',
			apply_filters(
				'memberhero_register_post_type_list',
				array(
					'labels'             => array(
						'name'                  => __( 'Member directories', 'memberhero' ),
						'singular_name'         => __( 'Member directory', 'memberhero' ),
						'menu_name'             => esc_html_x( 'Member directories', 'Admin menu name', 'memberhero' ),
						'add_new'               => __( 'Add member directory', 'memberhero' ),
						'add_new_item'          => __( 'Add new member directory', 'memberhero' ),
						'edit'                  => __( 'Edit', 'memberhero' ),
						'edit_item'             => __( 'Edit member directory', 'memberhero' ),
						'new_item'              => __( 'New member directory', 'memberhero' ),
						'view_item'             => __( 'View member directory', 'memberhero' ),
						'search_items'          => __( 'Search member directories', 'memberhero' ),
						'not_found'             => __( 'No member directories found', 'memberhero' ),
						'not_found_in_trash'    => __( 'No member directories found in trash', 'memberhero' ),
						'parent'                => __( 'Parent member directory', 'memberhero' ),
						'filter_items_list'     => __( 'Filter member directories', 'memberhero' ),
						'items_list_navigation' => __( 'Member directories navigation', 'memberhero' ),
						'items_list'            => __( 'Member directories directory', 'memberhero' ),
					),
					'description'         => __( 'This is where you can add and manage your community roles.', 'memberhero' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'memberhero_list',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_memberhero' ) ? 'memberhero' : true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title' ),
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => true,
				)
			)
		);

		do_action( 'memberhero_after_register_post_type' );
	}

	/**
	 * Flush rules if the event is queued.
	 */
	public static function maybe_flush_rewrite_rules() {
		if ( 'yes' === get_option( 'memberhero_queue_flush_rewrite_rules' ) ) {
			update_option( 'memberhero_queue_flush_rewrite_rules', 'no' );
			self::flush_rewrite_rules();
		}
	}

	/**
	 * Flush rewrite rules.
	 */
	public static function flush_rewrite_rules() {
		flush_rewrite_rules();
	}

}

MemberHero_Post_types::init();