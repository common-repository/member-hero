<?php
/**
 * Radio field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-data">

		<div class="memberhero-option">
			<?php
				echo esc_html( $field[ 'options' ][ $field[ 'value' ] ] );
			?>
		</div>

		<?php do_action( 'memberhero_radio_field_output', $field ); ?>

	</div>

</fieldset>