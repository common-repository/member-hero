<?php
/**
 * Profile cover dropdown.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-dropdown is-centered memberhero-dropdown-cover" id="memberhero-dropdown-cover">
	<?php memberhero_dropdown_caret(); ?>
	<ul>
		<?php echo memberhero_print_dropdown( 'cover', memberhero_get_cover_dropdown_items() ); ?>
	</ul>
</div>