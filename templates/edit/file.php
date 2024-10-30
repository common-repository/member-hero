<?php
/**
 * Text field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-input <?php echo implode( ' ', $field['control_class'] ); ?>">

		<div class="memberhero-fileinput-wrap">
			<input type="file" class="memberhero-fileinput" id="<?php echo esc_attr( $field['key'] ); ?>" name="<?php echo esc_attr( $field['key'] ); ?>" />

			<label for="<?php echo esc_attr( $field['key'] ); ?>" class="memberhero-button nav">
				<?php echo ! empty( $field[ 'icon' ] ) ? memberhero_svg_icon( $field[ 'icon' ] ) : ''; ?>
				<span>
					<?php
						echo ( ! empty( $field[ 'placeholder' ] ) ) ? esc_html( $field[ 'placeholder' ] ) : esc_html_e( 'Choose a file', 'memberhero' );
					?>
				</span>
			</label>
		</div>

	</div>

	<?php if ( ! empty( $field['helper'] ) ) : ?>
		<div class="memberhero-helper">
			<?php echo wp_kses_post( $field['helper'] ); ?>
		</div>
	<?php endif; ?>

</fieldset>