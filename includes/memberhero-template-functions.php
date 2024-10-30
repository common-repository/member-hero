<?php
/**
 * Template Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show or hide admin bar based on user cap.
 */
function memberhero_show_admin_bar() {
	$admin_bar = false;
	if ( current_user_can( 'memberhero_view_adminbar' ) ) {
		$admin_bar = true;
	}
	return apply_filters( 'memberhero_show_admin_bar', $admin_bar );
}

/**
 * Handle redirects before content is output - hooked into template_redirect so is_page works.
 */
function memberhero_template_redirect() {
	global $wp_query, $wp;

	if ( isset( $wp->query_vars[ 'logout' ] ) ) {

		// Logout.
		$redirect = ! empty( $_REQUEST[ 'redirect_to' ] ) ? esc_url_raw( $_REQUEST[ 'redirect_to' ] ) : memberhero_get_page_permalink( 'account' );
		wp_safe_redirect( str_replace( '&amp;', '&', wp_logout_url( $redirect ) ) );
		exit;

	} elseif ( isset( $wp->query_vars[ 'logout' ] ) && 'true' === $wp->query_vars[ 'logout' ] ) {
		// Redirect to the correct logout endpoint.
		wp_safe_redirect( esc_url_raw( memberhero_get_account_endpoint_url( 'logout' ) ) );
		exit;

	}
}

/**
 * Profile photo dropdown template.
 */
function memberhero_user_photo_dropdown() {
	memberhero_get_template( 'dropdown/photo.php' );
}

/**
 * Profile cover dropdown template.
 */
function memberhero_user_cover_dropdown() {
	memberhero_get_template( 'dropdown/cover.php' );
}

/**
 * User dropdown template.
 */
function memberhero_user_dropdown_menu() {
	memberhero_get_template( 'dropdown/user.php' );
}

/**
 * User actions dropdown template.
 */
function memberhero_user_actions_dropdown() {
	memberhero_get_template( 'dropdown/user-actions.php' );
}

/**
 * User admin dropdown template.
 */
function memberhero_manage_user_dropdown() {
	memberhero_get_template( 'dropdown/user-admin.php' );
}

/**
 * Profile header template.
 */
function memberhero_profile_header() {
	memberhero_get_template( 'profile/header.php' );
}

/**
 * Profile info template.
 */
function memberhero_profile_info() {
	memberhero_get_template( 'profile/info.php' );
}

/**
 * Profile side meta template.
 */
function memberhero_profile_sidemeta() {
	memberhero_get_template( 'profile/meta.php' );
}

/**
 * Profile bio template.
 */
function memberhero_profile_bio() {
	global $the_user, $the_list;

	// Hidden from member directory?
	if ( ! empty( $the_list ) && $the_list->show_bio === 'no' ) {
		return;
	}

	memberhero_get_template( 'profile/bio.php' );
}

/**
 * Profile username template.
 */
function memberhero_profile_username() {
	memberhero_get_template( 'profile/username.php' );
}

/**
 * Profile buttons template.
 */
function memberhero_profile_buttons() {
	memberhero_get_template( 'profile/buttons.php' );
}

/**
 * Profile photo template.
 */
function memberhero_profile_photo() {
	memberhero_get_template( 'profile/photo.php' );
}

/**
 * Profile cover template.
 */
function memberhero_profile_cover() {
	global $the_form;

	if ( ! empty( $the_form ) && $the_form->show_cover === 'no' ) {
		return;
	}

	memberhero_get_template( 'profile/cover.php' );
}

/**
 * Profile content output.
 */
function memberhero_profile_content() {
	global $wp, $the_form, $the_user;

	// Hook before any profile content is displayed, probably for global notices.
	do_action( 'memberhero_pre_profile_content' );

	if ( isset( $the_user->donotshow ) ) {
		return;
	}

	$tab = memberhero_clean( get_query_var( 'memberhero_tab' ) );
	if ( has_action( 'memberhero_profile_' . $tab . '_endpoint' ) ) {
		do_action( 'memberhero_profile_' . $tab . '_endpoint' );
		return;
	}

	// If we reached thus far, load a form.
	$form_id = memberhero_get_profile_endpoint_form();
	if ( ! empty( $form_id ) ) {
		memberhero_get_template( 'form/form.php', array(
			'atts'			=> array(
				'first_button' => __( 'Save changes', 'memberhero' ),
			),
			'the_form'		=> ( isset( $the_form->id ) ) ? $the_form : memberhero_get_form( $form_id ),
			'the_user' 		=> $the_user,
		) );

		do_action( 'memberhero_profile_loaded', get_current_user_id(), $the_user->ID );
	}
}

/**
 * Profile admin menu template.
 */
function memberhero_profile_admin_menu() {

	// Only show this if current user can edit users.
	if ( current_user_can( 'memberhero_edit_users' ) && ! is_memberhero_my_profile() ) {
		memberhero_get_template( 'profile/admin.php' );
	}

}

/**
 * Profile user actions menu template.
 */
function memberhero_profile_actions_menu() {
	memberhero_get_template( 'profile/actions.php' );
}

/**
 * Profile nav template.
 */
function memberhero_profile_nav() {
	memberhero_get_template( 'profile/nav.php' );
}

/**
 * Profile nav items template.
 */
function memberhero_profile_nav_items() {
	memberhero_get_template( 'profile/nav-items.php' );
}

/**
 * Profile nav editing template.
 */
function memberhero_profile_nav_editing() {
	memberhero_get_template( 'profile/nav-editing.php' );
}

/**
 * Profile nav not editing template.
 */
function memberhero_profile_nav_not_editing() {
	memberhero_get_template( 'profile/nav-not-editing.php' );
}

/**
 * Display user posts tab.
 */
function memberhero_profile_posts_endpoint() {
	global $the_user;

	$args = apply_filters( 'memberhero_profile_posts_query_args', array(
		'author'         => $the_user->ID,
		'posts_per_page' => 10,
		'post_type'		 => 'post',
		'post_status'    => 'publish',
	) );

	$items = new WP_Query( $args );

	memberhero_get_template( 'posts/posts.php', array( 'items' => $items ) );

	// Let's reset the WP Query.
	wp_reset_postdata();
}

/**
 * Display user comments tab.
 */
function memberhero_profile_comments_endpoint() {
	global $the_user;

	$args = apply_filters( 'memberhero_profile_comments_query_args', array(
		'user_id'        => $the_user->ID,
		'number' 		 => 10,
		'offset'		 => 0,
	) );

	$items = get_comments( $args );

	memberhero_get_template( 'comments/comments.php', array( 'items' => $items ) );
}

/**
 * Include modals for user profile.
 */
function memberhero_after_profile_template() {

	// Get all modals in profile template.
	memberhero_get_template( 'modals/view-image.php' );
	memberhero_get_template( 'modals/photo.php' );
	memberhero_get_template( 'modals/remove-photo.php' );
	memberhero_get_template( 'modals/remove-cover.php' );
	memberhero_get_template( 'modals/block-user.php' );
	memberhero_get_template( 'modals/delete-user.php' );
	memberhero_get_template( 'modals/error.php' );

	do_action( 'memberhero_include_profile_modals' );
}

/**
 * Include modals for user account.
 */
function memberhero_after_account_template() {

	// Get all modals in account template.
	memberhero_get_template( 'modals/delete-account.php' );

	do_action( 'memberhero_include_account_modals' );
}

/**
 * Display profile tabs.
 */
function memberhero_profile_tabs() {
	memberhero_get_template( 'profile/tabs.php' );
}

/**
 * Display notice if user has blocked profile.
 */
function memberhero_show_if_blocked() {
	global $the_user;
	if ( isset( $the_user->donotshow ) ) {
		return;
	}

	// When the logged in user has blocked someone.
	if ( memberhero_user_has_blocked( $the_user->user_id ) ) {
		memberhero_get_template( 'global/blocked.php' );
		$the_user->donotshow = true;
	// When the profile owner has blocked current user.
	} else if ( memberhero_user_has_blocked_me( $the_user->user_id ) ) {
		memberhero_get_template( 'global/blocked-me.php' );
		$the_user->donotshow = true;
	}
}

/**
 * Display notice if current profile is private.
 */
function memberhero_show_if_private() {
	global $the_user;
	if ( isset( $the_user->donotshow ) ) {
		return;
	}

	if ( get_current_user_id() != $the_user->user_id ) {
		if ( $the_user->is_private() && ! current_user_can( 'memberhero_view_private' ) ) {
			memberhero_get_template( 'global/private-profile.php' );
			$the_user->donotshow = true;
		}
	}
}

/**
 * Shows an icon next to username whose profile is private.
 */
function memberhero_show_private_profile_icon() {
	global $the_user;

	if ( get_current_user_id() != $the_user->user_id ) {
		if ( $the_user->is_private() && ! current_user_can( 'memberhero_view_private' ) ) {
			echo '<span class="memberhero-profile-icon memberhero-profile-is-private tips" data-tip="' . __( 'Private Account', 'memberhero' ) . '">' . memberhero_svg_icon( 'lock' ) . '</span>';
		}
	}
}

/**
 * Add profile nav buttons.
 */
function memberhero_profile_nav_buttons() {
	// The nav buttons would not appear to a non-logged in user.
	if ( ! is_user_logged_in() ) {
		return;
	}
	?>
	<div class="memberhero-nav-buttons">
		<?php do_action( 'memberhero_profile_buttons' ); ?>
		<div class="memberhero-action-menudrop-wrap">
			<?php do_action( 'memberhero_profile_admin_menu' ); ?>
			<?php do_action( 'memberhero_profile_actions_menu' ); ?>
		</div>
	</div>
	<?php
}

/**
 * List user actions.
 */
function memberhero_list_actions() {
	memberhero_get_template( 'list/actions.php' );
}

/**
 * List search bar template.
 */
function memberhero_list_filters( $list ) {
	global $the_list;

	memberhero_get_template( 'list/filters.php', array( 'list' => $list ) );
}

/**
 * List loop template.
 */
function memberhero_list_loop( $list ) {
	memberhero_get_template( 'list/loop.php', array( 'list' => $list ) );
}

/**
 * Display member list pagination.
 */
function memberhero_list_pagination( $list = array() ) {
	$page_links = paginate_links(
		apply_filters( 'memberhero_list_pagination_args', array(
			'base' 		=> add_query_arg( 'wpage', '%#%' ),
			'format' 	=> '',
			'prev_text' => sprintf( wp_kses_post( __( '%s Previous', 'memberhero' ) ), memberhero_svg_icon( 'chevron-left' ) ),
			'next_text' => sprintf( wp_kses_post( __( 'Next %s', 'memberhero' ) ), memberhero_svg_icon( 'chevron-right' ) ),
			'total' 	=> $list[ 'pages' ],
			'current' 	=> $list[ 'page' ],
		)
	) );

	// Current page can't be above the total pages number.
	if ( $list[ 'page' ] > $list[ 'pages' ] ) {
		return;
	}

	if ( apply_filters( 'memberhero_list_page_links', $page_links, $list ) ) {
		memberhero_get_template( 'list/pagination.php', array( 'list' => $list, 'page_links' => $page_links ) );
	}
}

/**
 * Display this template when no users were found.
 */
function memberhero_list_no_users( $list = array() ) {
	memberhero_get_template( 'list/no-users.php', array( 'list' => $list ) );
}

/**
 * Include modals for member list.
 */
function memberhero_after_list_template() {
	memberhero_get_template( 'modals/block-user.php' );
	memberhero_get_template( 'modals/delete-user.php' );
	memberhero_get_template( 'modals/error.php' );
}

/**
 * Account nav template.
 */
function memberhero_account_nav() {
	memberhero_get_template( 'account/nav.php' );
}

/**
 * Account content output.
 */
function memberhero_account_content() {
	global $wp, $the_form, $the_user;

	if ( ! empty( $wp->query_vars ) ) {
		foreach ( $wp->query_vars as $key => $value ) {
			// Ignore pagename param.
			if ( 'pagename' === $key ) {
				continue;
			}

			// Action hooks has the first priority.
			if ( has_action( 'memberhero_account_' . $key . '_endpoint' ) ) {
				do_action( 'memberhero_account_' . $key . '_endpoint', $value );
				return;
			}
		}
	}

	// If we do not have an action hook, load the respective form.
	$form_id = memberhero_get_account_endpoint_form();

	if ( ! empty( $form_id ) ) {
		memberhero_get_template( 'form/form.php', array(
			'atts'			=> array(
				'first_button' => __( 'Save changes', 'memberhero' ),
			),
			'the_form'		=> ( isset( $the_form->id ) ) ? $the_form : memberhero_get_form( $form_id ),
			'the_user' 		=> memberhero_get_user( get_current_user_id() ),
		) );
	}
}

/**
 * Display the endpoint title above account content.
 */
function memberhero_show_endpoint_title() {
	$endpoint       = memberhero()->query->get_current_endpoint();
	if ( ! $endpoint ) {
		$endpoint = memberhero_get_account_default_endpoint();
	}
	$endpoint_title = memberhero()->query->get_endpoint_title( $endpoint );
	$endpoint_desc	= memberhero()->query->get_endpoint_desc( $endpoint );
	?>
	<h3>
		<?php esc_html_e( $endpoint_title ); ?>
		<?php if ( ! empty( $endpoint_desc ) ) : ?>
		<span><?php echo esc_html( $endpoint_desc ); ?></span>
		<?php endif; ?>
	</h3>
	<?php
}

/**
 * Display the blocked users tab.
 */
function memberhero_account_blocked_endpoint() {

	$users = get_user_meta( get_current_user_id(), '_memberhero_blocked_users', true );

	if ( $users ) {
		arsort( $users );
	}

	memberhero_get_template( 'account/blocked.php', array( 'users' => $users ) );
}

/**
 * Display the delete account tab.
 */
function memberhero_account_delete_endpoint() {

	memberhero_get_template( 'account/delete.php' );
}

/**
 * Replace browser title with the profile page title.
 */
function memberhero_document_page_title( $title_parts ) {
	global $wp_query;

	$user = esc_attr( get_query_var( 'memberhero_user' ) );

	// Add the profile name in title bar.
	if ( isset( $wp_query->query[ 'memberhero_user'] ) && $wp_query->query[ 'memberhero_user'] != '' ) {

		if ( $user_id = username_exists( $user ) ) {
			$tab  = memberhero_get_profile_endpoint();
			$menu = memberhero_get_profile_menu();
			$tabs = memberhero_get_profile_tabs();
			$keys = array_keys( memberhero_get_profile_tabs() );
			unset( $title_parts[ 'site' ] );

			$the_user = memberhero_get_user( $user_id );

			// Display a private user tab.
			if ( ! in_array( $tab, $keys ) ) {

				if ( $tab == 'edit' ) {
					$menu[ $tab ][ 'label' ] = __( 'Edit profile', 'memberhero' );
				}

				if ( isset( $menu[ $tab ] ) ) {
					$title_parts[ 'title' ] = sprintf( __( '%s / %s', 'memberhero' ), $menu[ $tab ][ 'label' ], get_bloginfo( 'name' ) );
				}

			// Display a public user tab. such as user posts.
			} else {
				if ( $tab && ! in_array( $tab, array( 'view' ) ) ) {
					$title_parts[ 'title' ] = sprintf( __( '%s by %s (@%s) / %s', 'memberhero' ), $tabs[ $tab ][ 'label' ], $the_user->get( 'display_name' ), $the_user->get( 'user_login' ), get_bloginfo( 'name' ) );
				} else {
					$title_parts[ 'title' ] = sprintf( __( '%s (@%s) / %s', 'memberhero' ), $the_user->get( 'display_name' ), $the_user->get( 'user_login' ), get_bloginfo( 'name' ) );
				}
			}

			// Allow hooks to modify the title.
			if ( $tab ) {
				$title_parts[ 'title' ] = apply_filters( 'memberhero_profile_endpoint_title', $title_parts[ 'title' ], $tab, $the_user );
			}
		}

	} else {

		// Account endpoint.
		$endpoint = memberhero()->query->get_current_endpoint();
		if ( $endpoint ) {
			unset( $title_parts[ 'site' ] );
			if ( $endpoint != memberhero_get_account_default_endpoint() ) {
				$title_parts[ 'title' ] = memberhero()->query->get_endpoint_title( $endpoint ) . ' - ' . __( 'Account', 'memberhero' ) . ' / ' . get_bloginfo( 'name' );
			} else {
				$title_parts[ 'title' ] = __( 'Account', 'memberhero' ) . ' / ' . get_bloginfo( 'name' );
			}

			// Allow hooks to modify the title.
			$title_parts[ 'title' ] = apply_filters( 'memberhero_account_endpoint_title', $title_parts[ 'title' ], $endpoint );
		}

	}

	return $title_parts;
}

/**
 * Show meta counts for 3rd party integrations.
 */
function memberhero_show_meta_counts() {
	$meta_counts = apply_filters( 'memberhero_profile_meta_counts', array() );

	if ( $meta_counts ) {
		echo '<div class="memberhero-profile-meta memberhero-extra-meta">';
		foreach( $meta_counts as $meta_count ) {
			echo wp_kses_post( $meta_count );
		}
		echo '</div>';
	}
}