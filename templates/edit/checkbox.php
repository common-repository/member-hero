<?php
/**
 * Checkbox field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $field['options'] ) ) {
	return;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-input <?php echo implode( ' ', $field['control_class'] ); ?>">

		<?php if ( ! empty( $field['helper'] ) ) : ?>

			<div class="memberhero-helper"><?php echo wp_kses_post( $field['helper'] ); ?></div>

		<?php endif; ?>

		<?php foreach ( $field['options'] as $key => $val ) { ?>
			<div class="memberhero-input memberhero-checkbox">
				<label>
					<input
						type="checkbox"
						name="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?><?php echo count( $field['options'] ) > 1 ? '[]' : ''; ?>"
						class="<?php echo implode( ' ', $field['input_class'] ); ?>" 
						value="<?php echo count( $field['options'] ) > 1 ? esc_attr( $key ) : 'yes'; ?>" 
						<?php echo implode( ' ', $field['attributes'] ); ?> 
						<?php

						if ( is_array( $field['value'] ) ) {
							checked( in_array( (string) $key, $field['value'], true ), true );
						} else {
							checked( $field['value'], (string) $key );
						}

						?> />
					<?php echo memberhero_svg_icon( 'check' ); ?>
					<?php echo esc_html( $val ); ?>
				</label>
			</div>
		<?php } ?>

	</div>

</fieldset>