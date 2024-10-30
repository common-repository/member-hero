<?php
/**
 * User dropdown
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-dropdown" id="memberhero-dropdown-user">
	<?php memberhero_dropdown_caret(); ?>
	<ul>
		<?php echo memberhero_print_dropdown( 'user', memberhero_get_user_dropdown_items() ); ?>
	</ul>
</div>