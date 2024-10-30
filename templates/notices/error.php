<?php
/**
 * Show error messages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $messages ) {
	return;
}

?>

<?php if ( count( $messages ) > 1 ) : ?>

	<ul class="memberhero-errors" role="alert">
		<li class="memberhero-error-header"><?php esc_html_e( 'Please correct the following errors:', 'memberhero' ); ?></li>
		<?php foreach ( $messages as $message ) : ?>
			<li>
				<?php
					echo memberhero_kses_notice( $message );
				?>
			</li>
		<?php endforeach; ?>
	</ul>

<?php else : ?>

	<?php foreach ( $messages as $message ) : ?>
		<div class="memberhero-error" role="alert">
			<?php
				echo memberhero_kses_notice( $message );
			?>
		</div>
	<?php endforeach; ?>

<?php endif; ?>