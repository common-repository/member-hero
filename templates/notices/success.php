<?php
/**
 * Show messages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $messages ) {
	return;
}

?>

<?php foreach ( $messages as $message ) : ?>
	<div class="memberhero-message" role="alert">
		<?php
			echo memberhero_kses_notice( $message );
		?>
	</div>
<?php endforeach; ?>