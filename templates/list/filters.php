<?php
/**
 * Member list filters and search bar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_list_filters' );

?>

<div class="memberhero-filters">

	<?php memberhero_get_template( 'list/search.php', array( 'list' => $list ) ); ?>

	<div class="memberhero-filters-right">

		<?php memberhero_get_template( 'list/sorting.php', array( 'list' => $list ) ); ?>

	</div>

</div>

<?php do_action( 'memberhero_after_list_filters' ); ?>