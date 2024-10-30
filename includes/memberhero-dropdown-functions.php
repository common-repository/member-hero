<?php
/**
 * Dropdown Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display the dropdown caret.
 */
function memberhero_dropdown_caret() {
	?>
	<div class="memberhero-dropdown-caret">
		<span class="memberhero-caret-outer"></span>
		<span class="memberhero-caret-inner"></span>
	</div>
	<?php
}

/**
 * Print a dropdown menu content.
 */
function memberhero_print_dropdown( $menu, $items ) {
	if ( empty( $items ) || empty( $menu ) ) {
		return;
	}

	// Loop through items.
	foreach( (array) $items as $id => $item ) {
		
		if ( ! empty( $item[ 'icon' ] ) ) {
			$item[ 'html' ] = preg_replace( '/<a (.*?)>(.*?)<\/a>/', '<a $1 ><span class="memberhero-dropdown-icon">' . memberhero_svg_icon( $item[ 'icon' ] ) . '</span>$2</a>', $item[ 'html' ] );
		}
		?>

		<?php do_action( "memberhero_{$menu}_dropdown_before_{$id}" ); ?>
		<li class="<?php echo ! empty( $item[ 'class' ] ) ? esc_attr( $item[ 'class' ] ) : ''; ?>">
			<?php echo $item[ 'html' ]; ?>
		</li>
		<?php do_action( "memberhero_{$menu}_dropdown_after_{$id}" ); ?>

	<?php
	}
}


/**
 * Get dropdown items for cover.
 */
function memberhero_get_cover_dropdown_items() {
	return memberhero_get_photo_dropdown_items( 'cover' );
}

/**
 * Get dropdown items for avatar.
 */
function memberhero_get_avatar_dropdown_items() {
	return memberhero_get_photo_dropdown_items( 'avatar' );
}

/**
 * Get dropdown items for user.
 */
function memberhero_get_user_dropdown_items() {
	global $current_user;

	$items = array();

	$items[ 'name' ] = array(
		'class'		=> 'profile-name',
		'html'		=> '<a href="' . memberhero_get_profile_url( $current_user->user_login ) . '">
							<b>' . esc_attr( $current_user->get( 'display_name' ) ) . '</b>
							<span>@' . esc_attr( $current_user->get( 'user_login' ) ) . '</span>
						</a>',
	);

	$items[ 'seperator1' ] = array(
		'class'		=> 'memberhero-dropdown-divider',
		'html' 		=> '',
	);

	$items[ 'profile' ] = array(
		'class'		=> '',
		'icon'		=> 'user',
		'html' 		=> '<a href="' . memberhero_get_profile_url( $current_user->user_login ) . '">' . __( 'Profile', 'memberhero' ) . '</a>',
	);

	$items[ 'seperator2' ] = array(
		'class'		=> 'memberhero-dropdown-divider',
		'html' 		=> '',
	);

	$items[ 'settings' ] = array(
		'class'		=> '',
		'icon'		=> 'settings',
		'html'		=> '<a href="' . memberhero_get_page_permalink( 'account' ) . '">' . __( 'Account and settings', 'memberhero' ) . '</a>',
	);

	$items[ 'logout' ] = array(
		'class'		=> '',
		'icon'		=> 'log-out',
		'html'		=> '<a href="' . memberhero_logout_url( memberhero_get_current_url() ) . '">' . sprintf( __( 'Log out @%s', 'memberhero' ), $current_user->user_login ) . '</a>',
	);

	return apply_filters( 'memberhero_get_user_dropdown_items', $items, $current_user );
}

/**
 * Get dropdown items for user actions.
 */
function memberhero_get_user_actions_dropdown_items() {
	global $the_user, $logged_user;

	$items = array();

	$user_id 	= absint( $the_user->user_id );
	$user 		= esc_attr( $the_user->get( 'user_login' ) );

	$items[ 'block' ] = array(
		'class'		=> '',
		'icon'		=> 'eye-off',
		'html' 		=> '<a href="#" data-user_id="' . $user_id . '" data-user="' . $user . '" class="memberhero-modal-open memberhero_block_user ' . $logged_user->get_block_class( $user_id ) . '" rel="memberhero-modal-block">' . sprintf( __( 'Block @%s', 'memberhero' ), $user ) . '</a>',
	);

	$items[ 'unblock' ] = array(
		'class'		=> '',
		'icon'		=> 'eye',
		'html' 		=> '<a href="#" data-user_id="' . $user_id . '" data-user="' . $user . '" data-action="unblock_user" class="memberhero-ajax-action memberhero_unblock_user ' . $logged_user->get_block_class( $user_id ) . '">' . sprintf( __( 'Unblock @%s', 'memberhero' ), $user ) . '</a>',
	);

	return apply_filters( 'memberhero_get_user_actions_dropdown_items', $items, $the_user, $logged_user );
}

/**
 * Get dropdown items for managing a user.
 */
function memberhero_get_user_admin_dropdown_items() {
	global $the_user;

	$items = array();

	$user_id = absint( $the_user->user_id );
	$user = $the_user->get( 'user_login' );

	// Approve/reject links.
	if ( is_memberhero_rejected_user( $user_id ) ) {
		$items[ 'approve' ] = array(
			'class'		=> '',
			'icon'		=> 'user-check',
			'html' 		=> '<a href="#" data-user_id="' . $user_id . '" data-user="' . $user . '" data-action="approve_user" class="memberhero-ajax-action">' . sprintf( __( 'Approve @%s', 'memberhero' ), $user ) . '</a>',
		);
		$items[ 'seperator2' ] = array(
			'class'		=> 'memberhero-dropdown-divider',
			'html' 		=> '',
		);
	} else if ( is_memberhero_pending_user( $user_id ) ) {
		$items[ 'approve' ] = array(
			'class'		=> '',
			'icon'		=> 'user-check',
			'html' 		=> '<a href="#" data-user_id="' . $user_id . '" data-user="' . $user . '" data-action="approve_user" class="memberhero-ajax-action">' . sprintf( __( 'Approve @%s', 'memberhero' ), $user ) . '</a>',
		);
		$items[ 'reject' ] = array(
			'class'		=> '',
			'icon'		=> 'user-x',
			'html' 		=> '<a href="#" data-user_id="' . $user_id . '" data-user="' . $user . '" data-action="reject_user" class="memberhero-ajax-action">' . sprintf( __( 'Reject @%s', 'memberhero' ), $user ) . '</a>',
		);
		$items[ 'seperator2' ] = array(
			'class'		=> 'memberhero-dropdown-divider',
			'html' 		=> '',
		);
	}

	// Edit frontend profile link.
	$items[ 'edit_front' ] = array(
		'class'		=> '',
		'icon'		=> 'edit-2',
		'html' 		=> '<a href="' . esc_url( memberhero_get_profile_endpoint_url( 'edit' ) ) . '">' . sprintf( __( 'Edit @%s', 'memberhero' ), $user ) . '</a>',
	);

	// Ensure user has access to WP admin and can edit the user.
	if ( current_user_can( 'edit_user', $user_id ) ) {
		$items[ 'edit_backend' ] = array(
			'class'		=> '',
			'icon'		=> 'edit-3',
			'html' 		=> '<a href="' . esc_url ( get_edit_user_link( $user_id ) ) . '">' . sprintf( __( 'Edit @%s (WP)', 'memberhero' ), $user ) . '</a>',
		);
	}

	// Check that user want to log in as another user.
	if ( $user_id != get_current_user_id() ) {
		$items[ 'login_as' ] = array(
			'class'		=> '',
			'icon'		=> 'log-in',
			'html' 		=> '<a href="' . add_query_arg( 'memberhero_logon', memberhero_md5( $user_id ), add_query_arg( 'memberhero_id', $user_id ) ) . '">' . sprintf( __( 'Log in as @%s', 'memberhero' ), $user ) . '</a>',
		);
	}

	// Super admins should not see this.
	if ( current_user_can( 'memberhero_delete_users' ) && ! is_super_admin( $user_id ) ) {
		$items[ 'seperator2' ] = array(
			'class'		=> 'memberhero-dropdown-divider',
			'html' 		=> '',
		);

		$items[ 'delete' ] = array(
			'class'		=> '',
			'icon'		=> 'trash',
			'html' 		=> '<a href="#" data-user_id="' . $user_id . '" data-user="' . $user . '" class="memberhero-modal-open memberhero_delete_user" rel="memberhero-modal-user-delete">' . sprintf( __( 'Delete @%s', 'memberhero' ), $user ) . '</a>',
		);
	}

	return apply_filters( 'memberhero_get_user_admin_dropdown_items', $items, $the_user );
}

/**
 * Get dropdown items for photo.
 */
function memberhero_get_photo_dropdown_items( $name ) {
	global $the_user;

	$items = array();

	$items[ 'upload' ] = array(
		'class'		=> 'memberhero-dropdown-upload',
		'html' 		=> call_user_func_array( 'memberhero_get_photo_upload_html', array( 'name' => $name ) ),
	);

	// Add "remove" link.
	$items[ 'remove' ] = array(
		'class' => call_user_func( "memberhero_user_uploaded_{$name}" ) ? "memberhero_has_{$name}" : "memberhero_no_{$name}",
		'html' 	=> '<a href="#" data-user_id="' . $the_user->user_id . '" class="memberhero-modal-open" rel="memberhero-modal-remove-' . $name . '">' . __( 'Remove', 'memberhero' ) . '</a>',
	);

	// Divider.
	$items[ 'seperator1' ] = array(
		'class'		=> 'memberhero-dropdown-divider',
		'html' 		=> '',
	);

	$items[ 'cancel' ] = array(
		'class'		=> '',
		'html' 		=> '<a href="#" rel="memberhero_dropdown_hide">' . __( 'Cancel', 'memberhero' ) . '</a>',
	);

	return apply_filters( "memberhero_get_{$name}_dropdown_items", $items, $the_user );
}

/**
 * Get photo upload html.
 */
function memberhero_get_photo_upload_html( $name = 'avatar' ) {
	global $the_user;

	ob_start();
	?>
	<button type="button" class="memberhero-dropdown-link"><?php esc_html_e( 'Upload photo', 'memberhero' ); ?></button>
	<div class="memberhero-photo-selector">
		<div class="memberhero-image-selector">
			<form>
				<input 
					type="file" 
					name="_memberhero_profile_<?php echo $name; ?>" 
					class="memberhero-file-input memberhero-input-<?php echo $name; ?>" 
					tabindex="-1" 
					accept="image/gif,image/jpeg,image/jpg,image/png" 
					data-user_id="<?php echo $the_user->user_id; ?>"
				>
			</form>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Get dropdown items for sorting members.
 */
function memberhero_get_sort_members_dropdown_items() {
	global $the_user;

	$items = array();

	foreach( memberhero_get_sorting_options() as $key => $name ) {
		$items[ $key ] = array(
			'class' => memberhero_get_current_sort() === $key ? 'is-selected' : '',
			'html'  => '<a href="' . add_query_arg( 'wsort', memberhero_clean( $key ) ) . '">' . esc_html( $name ) . '</a>',
		);
	}

	return apply_filters( 'memberhero_get_sort_members_dropdown_items', $items );
}