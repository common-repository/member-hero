<?php
/**
 * Email field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-input <?php echo implode( ' ', $field['control_class'] ); ?>">

		<?php memberhero_get_template( 'field/icon.php', array( 'field' => $field ) ); ?>

		<input 
				type="text" 
				name="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?>" 
				id="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?>" 
				value="<?php echo esc_attr( $field['value'] ); ?>" 
				class="<?php echo implode( ' ', $field['input_class'] ); ?>" 
				<?php echo implode( ' ', $field['attributes'] ); ?> 
		/>

	</div>

	<?php if ( ! empty( $field['helper'] ) ) : ?>
		<div class="memberhero-helper"><?php echo wp_kses_post( $field['helper'] ); ?></div>
	<?php endif; ?>

	<?php do_action( 'memberhero_after_email_field_input' ); ?>

</fieldset>