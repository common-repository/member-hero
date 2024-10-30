<?php
/**
 * Member list user actions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-usercard-actions">

	<?php do_action( 'memberhero_before_usercard_actions' ); ?>

	<?php do_action( 'memberhero_profile_admin_menu' ); ?>
	<?php do_action( 'memberhero_profile_actions_menu' ); ?>

	<?php do_action( 'memberhero_after_usercard_actions' ); ?>

</div>