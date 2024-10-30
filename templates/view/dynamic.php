<?php
/**
 * Dynamic field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-data">

		<?php
			if ( function_exists( 'memberhero_get_' . $field[ 'key' ] ) ) {
				echo call_user_func( 'memberhero_get_' . $field[ 'key' ] );
			}
		?>

		<?php do_action( 'memberhero_dynamic_field_output', $field ); ?>

	</div>

</fieldset>