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

	<div class="memberhero-data">

		<?php
			$value = isset( $field[ 'options' ][ $field[ 'value' ] ] ) ? $field[ 'options' ][ $field[ 'value' ] ] : $field[ 'value' ];

			echo esc_html( $value );
		?>

		<?php do_action( 'memberhero_select_field_output', $field ); ?>

	</div>

</fieldset>