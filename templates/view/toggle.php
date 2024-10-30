<?php
/**
 * Toggle field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $field[ 'value' ] ) ) {
	return;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-data">

		<?php echo ! empty( $field[ 'value' ] ) && $field[ 'value' ] === 'yes' ? __( 'Yes', 'memberhero' ) : __( 'No', 'memberhero' ); ?>

		<?php do_action( 'memberhero_toggle_field_output', $field ); ?>

	</div>

</fieldset>