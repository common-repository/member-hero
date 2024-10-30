<?php
/**
 * Toggle field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field, 'helper' => ! empty( $field['helper'] ) ) ); ?>

	<div class="memberhero-input <?php echo implode( ' ', $field['control_class'] ); ?>">

		<input 
				type="checkbox" 
				name="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?>" 
				id="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?>" 
				value="<?php echo esc_attr( $field['value'] ); ?>" 
				class="<?php echo implode( ' ', $field['input_class'] ); ?>" 
				<?php echo implode( ' ', $field['attributes'] ); ?> 
				<?php checked( $field['value'], 1, true ) ?>
		/>

		<?php if ( ! empty( $field['helper'] ) ) : ?>

			<div class="memberhero-helper memberhero-helper-label"><?php echo wp_kses_post( $field['helper'] ); ?></div>
			<div class="memberhero-toggle" data-toggle-on="<?php echo $field['value'] === 'yes' ? 1 : 0; ?>" tabindex="0"></div>

		<?php else : ?>

			<div class="memberhero-toggle" data-toggle-on="<?php echo $field['value'] === 'yes' ? 1 : 0; ?>" tabindex="0"></div>

		<?php endif; ?>

	</div>

</fieldset>