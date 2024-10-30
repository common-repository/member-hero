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

	<div class="memberhero-input <?php echo implode( ' ', $field['control_class'] ); ?>">

		<textarea 
				name="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?>" 
				id="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?>" 
				class="<?php echo implode( ' ', $field['input_class'] ); ?>" 
				cols="20" 
				rows="5" 
				<?php echo implode( ' ', $field['attributes'] ); ?>
		><?php echo esc_textarea( $field['value'] ); ?></textarea>

	</div>

</fieldset>