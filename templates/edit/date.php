<?php
/**
 * Date field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-input memberhero-input-grid <?php echo implode( ' ', $field['control_class'] ); ?>">

		<?php memberhero_dropdown_days( $field ); ?>

		<?php memberhero_dropdown_months( $field ); ?>

		<?php memberhero_dropdown_years( $field ); ?>

	</div>

	<?php if ( ! empty( $field['helper'] ) ) : ?>
		<div class="memberhero-helper"><?php echo wp_kses_post( $field['helper'] ); ?></div>
	<?php endif; ?>

</fieldset>