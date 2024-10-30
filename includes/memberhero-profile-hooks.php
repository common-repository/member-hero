<?php
/**
 * Profile Hooks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add profile <meta> to the html header.
 */
function memberhero_add_profile_meta() {
	global $the_user;

	if ( ! is_memberhero_profile_page() ) {
		return;
	}

	$the_user = memberhero_get_user( memberhero_get_active_profile_id() );

	if ( ! $the_user->get( 'description' ) ) {
		$meta = sprintf( __( 'Check %s profile on %s.', 'memberhero' ), '@' . $the_user->user_login, esc_html( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) );
	} else {
		$meta = memberhero_get_user_description();
	}
	?>
	<meta name="description" content="<?php echo esc_attr( wp_strip_all_tags( preg_replace( '/[\r\n\t ]+/', ' ', $meta ) ) ); ?>" />
	<?php
}
add_action( 'wp_head', 'memberhero_add_profile_meta' );

/**
 * Add social links to the user.
 */
function memberhero_add_social_links() {
	global $the_form, $the_list, $the_user;

	// Show social links when they're enabled.
	if ( ( isset( $the_form->show_social ) && $the_form->show_social !== 'no' ) || ( isset( $the_list->show_social ) && $the_list->show_social !== 'no' ) ) {
		$links = memberhero_get_social_links( $the_user->ID );

		if ( empty( $links ) ) {
			return;
		}

		// Show template.
		memberhero_get_template( 'global/social-links.php', array( 'links' => $links ) );
	}
}
add_action( 'memberhero_after_profile_username', 'memberhero_add_social_links', 20 );

/**
 * Filter for user description to avoid duplicates.
 */
function memberhero_getmeta_description( $output, $the_user ) {
	global $the_user;

	if ( isset( $the_user->bio_shown ) && $the_user->bio_shown === true && memberhero_get_scope() == 'view' ) {
		return '';
	}

	return $output;
}
add_filter( 'memberhero_getmeta_description', 'memberhero_getmeta_description', 20, 2 );