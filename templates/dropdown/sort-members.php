<?php
/**
 * Sort members dropdown.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-dropdown memberhero-dropdown-sort">
	<?php memberhero_dropdown_caret(); ?>
	<ul>
		<?php echo memberhero_print_dropdown( 'sort_members', memberhero_get_sort_members_dropdown_items() ); ?>
	</ul>
</div>