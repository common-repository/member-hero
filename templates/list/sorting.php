<?php
/**
 * Member list sorting.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-filters-sort">

	<a href="#" class="memberhero-button nav memberhero-dropdown-init" rel="memberhero-dropdown-sort"><?php echo memberhero_svg_icon( 'bar-chart' ); ?><span class="memberhero-small-label"><?php printf( __( 'Sort by: %s', 'memberhero' ), memberhero_get_current_sort_label() ); ?></span></a>

	<?php memberhero_get_template( 'dropdown/sort-members.php' ); ?>

</div>