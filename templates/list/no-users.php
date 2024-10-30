<?php
/**
 * Member list no results.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_list_no_users', $list );

?>

	<h1 class="memberhero-notice">
		<?php esc_html_e( 'Oops!', 'memberhero' ); ?>
	</h1>

	<p class="memberhero-notice-text">
		<?php printf( wp_kses_post( __( 'We could not find any users. Please <a href="%s">go back</a> and try another search.', 'memberhero' ) ), memberhero_get_current_url_clean() ); ?>
	</p>

<?php

do_action( 'memberhero_after_list_no_users', $list );