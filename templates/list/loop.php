<?php
/**
 * Member list loop.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-grid-content" <?php echo memberhero_list_container_styles(); ?>>

	<?php foreach( $list[ 'users' ] as $user ) : $the_user = memberhero_get_user( $user->ID ); ?>

	<div class="memberhero-grid-item" id="memberhero-user-<?php echo $user->ID; ?>">

		<?php do_action( 'memberhero_list_card', $list ); ?>

	</div>

	<?php endforeach; ?>

</div>