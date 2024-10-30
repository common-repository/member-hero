<?php
/**
 * Profile photo dropdown.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-dropdown is-centered memberhero-dropdown-photo" id="memberhero-dropdown-photo">
	<?php memberhero_dropdown_caret(); ?>
	<ul>
		<?php echo memberhero_print_dropdown( 'avatar', memberhero_get_avatar_dropdown_items() ); ?>
	</ul>
</div>