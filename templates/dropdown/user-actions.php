<?php
/**
 * User actions dropdown
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-dropdown memberhero-dropdown-useractions">
	<?php memberhero_dropdown_caret(); ?>
	<ul>
		<?php echo memberhero_print_dropdown( 'user_actions', memberhero_get_user_actions_dropdown_items() ); ?>
	</ul>
</div>