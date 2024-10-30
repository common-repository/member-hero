<?php
/**
 * Blocked user message.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-section is-centered is-warning">
	<div class="memberhero-strong-heading"><?php echo sprintf( __( '@%s is blocked', 'memberhero' ), $the_user->user_login ); ?></div>
	<div class="memberhero-subheading"><?php esc_html_e( 'Unblock to view their profile details and posts.', 'memberhero' ); ?></div>
	<div class="memberhero-subheading alt">
		<a href="#" 
			data-user_id="<?php echo absint( $the_user->user_id ); ?>" 
			data-user="<?php echo esc_attr( $the_user->get( 'user_login' ) ); ?>" 
			data-action="unblock_user" 
			data-reload="true" 
			class="memberhero-button nav memberhero-ajax-action memberhero_unblock_user <?php echo $logged_user->get_block_class( absint( $the_user->user_id ) ); ?>"><?php _e( 'Unblock', 'memberhero' ); ?></a>
	</div>
</div>