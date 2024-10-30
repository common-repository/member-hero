<?php
/**
 * Number field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-data">

		<?php echo esc_html( $field[ 'value' ] ); ?>

		<?php do_action( 'memberhero_number_field_output', $field ); ?>

	</div>

</fieldset>