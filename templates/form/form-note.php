<?php
/**
 * Displays the top note.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( ! empty( $top_note ) ) : ?>

	<div class="memberhero-note"><?php echo esc_html( $top_note ); ?></div>

<?php endif; ?>