<?php
/**
 * My Profile bio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_profile_bio' );

?>

	<?php
		if ( $the_user->get( 'description' ) ) :
	?>

		<div class="memberhero-profile-bio">
			<?php echo memberhero_get_user_description(); ?>
		</div>

	<?php
		$the_user->bio_shown = true;
		endif;
	?>

<?php do_action( 'memberhero_after_profile_bio' ); ?>