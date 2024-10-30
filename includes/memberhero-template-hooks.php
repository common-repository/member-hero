<?php
/**
 * Template Hooks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin bar.
 */
add_filter( 'show_admin_bar', 'memberhero_show_admin_bar', 50 );

/**
 * Hook before output.
 */
add_action( 'template_redirect', 'memberhero_template_redirect', 10 );

/**
 * Dropdown menus.
 */
add_action( 'memberhero_user_photo_dropdown', 'memberhero_user_photo_dropdown' );
add_action( 'memberhero_user_cover_dropdown', 'memberhero_user_cover_dropdown' );
add_action( 'memberhero_user_dropdown_menu', 'memberhero_user_dropdown_menu' );
add_action( 'memberhero_user_actions_dropdown', 'memberhero_user_actions_dropdown' );
add_action( 'memberhero_manage_user_dropdown', 'memberhero_manage_user_dropdown' );

/**
 * Profile.
 */
add_action( 'memberhero_profile_header', 'memberhero_profile_header' );
add_action( 'memberhero_profile_info', 'memberhero_profile_info' );
add_action( 'memberhero_profile_sidemeta', 'memberhero_profile_sidemeta' );
add_action( 'memberhero_profile_bio', 'memberhero_profile_bio' );
add_action( 'memberhero_profile_username', 'memberhero_profile_username' );
add_action( 'memberhero_profile_buttons', 'memberhero_profile_buttons' );
add_action( 'memberhero_profile_photo', 'memberhero_profile_photo' );
add_action( 'memberhero_profile_cover', 'memberhero_profile_cover' );
add_action( 'memberhero_profile_content', 'memberhero_profile_content' );
add_action( 'memberhero_profile_admin_menu', 'memberhero_profile_admin_menu' );
add_action( 'memberhero_profile_actions_menu', 'memberhero_profile_actions_menu' );

// Provile nav items.
add_action( 'memberhero_profile_nav', 'memberhero_profile_nav' );
add_action( 'memberhero_profile_nav_items', 'memberhero_profile_nav_items' );
add_action( 'memberhero_profile_nav_editing', 'memberhero_profile_nav_editing' );
add_action( 'memberhero_profile_nav_not_editing', 'memberhero_profile_nav_not_editing' );

// Display profile tabs.
add_action( 'memberhero_profile_posts_endpoint', 'memberhero_profile_posts_endpoint' );
add_action( 'memberhero_profile_comments_endpoint', 'memberhero_profile_comments_endpoint' );

// After profile template.
add_action( 'memberhero_after_profile_template', 'memberhero_after_profile_template' );

// Profile tabs.
add_action( 'memberhero_profile_tabs', 'memberhero_profile_tabs' );

// Hooks for profile content.
add_action( 'memberhero_pre_profile_content', 'memberhero_show_if_blocked', 10 );
add_action( 'memberhero_pre_profile_content', 'memberhero_show_if_private', 20 );

// Hooks for profile name.
add_action( 'memberhero_after_profile_name', 'memberhero_show_private_profile_icon', 10 );

// Hooks for profile nav buttons.
add_action( 'memberhero_profile_nav_buttons', 'memberhero_profile_nav_buttons' );

/**
 * Member list.
 */
add_action( 'memberhero_list_card', 'memberhero_profile_cover', 20 );
add_action( 'memberhero_list_card', 'memberhero_profile_photo', 50 );
add_action( 'memberhero_list_card', 'memberhero_profile_username', 80 );
add_action( 'memberhero_list_card', 'memberhero_profile_bio', 140 );

// Member list loop and pagination.
add_action( 'memberhero_list_top', 'memberhero_list_filters', 10 );
add_action( 'memberhero_list_loop', 'memberhero_list_loop' );
add_action( 'memberhero_list_pagination', 'memberhero_list_pagination' );
add_action( 'memberhero_list_no_users', 'memberhero_list_no_users' );

// After member list template.
add_action( 'memberhero_after_list_template', 'memberhero_after_list_template' );

/**
 * Account.
 */
add_action( 'memberhero_account_nav', 'memberhero_account_nav' );
add_action( 'memberhero_account_content', 'memberhero_account_content' );
add_action( 'memberhero_before_account_form', 'memberhero_show_endpoint_title' );

// Account page endpoints.
add_action( 'memberhero_account_blocked_endpoint', 'memberhero_account_blocked_endpoint' );
add_action( 'memberhero_account_delete_endpoint', 'memberhero_account_delete_endpoint' );

add_action( 'memberhero_after_account_template', 'memberhero_after_account_template' );

/**
 * Page title.
 */
add_filter( 'document_title_parts', 'memberhero_document_page_title' );

/**
 * Footer.
 */
add_action( 'wp_footer', 'memberhero_print_js', 25 );