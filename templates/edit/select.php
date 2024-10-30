<?php
/**
 * Select field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-input <?php echo implode( ' ', $field['control_class'] ); ?>">

		<select
			name="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?><?php echo ( ! empty( $field['multiselect'] ) ) ? '[]' : ''; ?>"
			id="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?>"
			class="<?php echo implode( ' ', $field['input_class'] ); ?>" 
			<?php echo implode( ' ', $field['attributes'] ); ?>
			<?php echo ! empty( $field['multiselect'] ) ? 'multiple="multiple"' : ''; ?>
		>
		<?php
			if ( ! empty( $field[ 'options' ] ) ) :
				foreach ( $field['options'] as $key => $val ) :
					?>
					<option value="<?php echo esc_attr( $key ); ?>"
						<?php

						if ( is_array( $field['value'] ) ) {
							selected( in_array( (string) $key, $field['value'], true ), true );
						} else {
							selected( $field['value'], (string) $key );
						}

						?>
					>
					<?php echo esc_html( $val ); ?></option>
					<?php
				endforeach;
			endif;
		?>
		</select>

	</div>

	<?php if ( ! empty( $field['helper'] ) ) : ?>
		<div class="memberhero-helper"><?php echo wp_kses_post( $field['helper'] ); ?></div>
	<?php endif; ?>

</fieldset>