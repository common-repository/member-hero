<?php
/**
 * Textarea field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-data">

		<?php echo memberhero_get_textarea( $field ); ?>

		<?php do_action( 'memberhero_textarea_field_output', $field ); ?>

	</div>

</fieldset>