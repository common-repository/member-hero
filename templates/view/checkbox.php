<?php
/**
 * Checkbox field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-data">

		<?php
			if ( is_array( $field[ 'value' ] ) ) {
				foreach( $field[ 'value' ] as $option ) {
				?>
					<div class="memberhero-option">
						<?php echo esc_html( $field[ 'options' ][ $option ] ); ?>
					</div>
				<?php
				}
			}
		?>

		<?php do_action( 'memberhero_checkbox_field_output', $field ); ?>

	</div>

</fieldset>