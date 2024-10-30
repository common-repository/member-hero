<?php
/**
 * Member list pagination.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_list_pagination', $page_links, $list ); ?>

<div class="memberhero-paginate">

	<?php echo $page_links; ?>

</div>

<?php do_action( 'memberhero_after_list_pagination', $page_links, $list ); ?>