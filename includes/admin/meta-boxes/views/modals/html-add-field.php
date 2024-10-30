<?php
/**
 * Modal: Add a custom field.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="memberhero-modal" id="memberhero-add-field">

	<h3><?php esc_html_e( 'Create a Custom Field', 'memberhero' ); ?></h3>

	<?php memberhero_init_custom_field_options(); ?>

	<div class="modal-footer">
		<a href="#" class="button button-primary add_field"><?php esc_html_e( 'Create &rarr;', 'memberhero' ); ?></a>
		<a href="#memberhero-add-element" class="button button-secondary" rel="modal:open"><?php esc_html_e( 'Cancel', 'memberhero' ); ?></a>
	</div>

</div>