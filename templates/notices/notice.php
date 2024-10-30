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
	<div class="memberhero-info">
		<?php
			echo memberhero_kses_notice( $message );
		?>
	</div>
<?php endforeach; ?>