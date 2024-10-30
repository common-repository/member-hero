<?php
/**
 * A private profile.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-section is-centered is-warning">
	<div class="memberhero-strong-heading"><?php echo __( 'Private account', 'memberhero' ); ?></div>
	<div class="memberhero-subheading"><?php echo sprintf( __( '@%s profile is private. You cannot see their profile.', 'memberhero' ), $the_user->user_login ); ?></div>
</div>