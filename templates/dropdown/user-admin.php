<?php
/**
 * User admin dropdown
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-dropdown memberhero-dropdown-useradmin">
	<?php memberhero_dropdown_caret(); ?>
	<ul>
		<?php echo memberhero_print_dropdown( 'user_admin', memberhero_get_user_admin_dropdown_items() ); ?>
	</ul>
</div>