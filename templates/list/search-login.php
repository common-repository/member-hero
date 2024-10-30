<?php
/**
 * Member list login to search.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-search-form-wrap">

	<div class="memberhero-info">
		<?php printf( wp_kses_post( __( 'Please <a href="%s">login</a> to search members. Not a member? <a href="%s">Sign up</a>.', 'memberhero' ) ), memberhero_login_url(), memberhero_register_url() ); ?>
	</div>

</div>