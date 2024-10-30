<?php
/**
 * Rating field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-data">

		<div class="memberhero-star" data-readonly="true" data-score="<?php echo absint( $field[ 'value' ] ); ?>" data-key="<?php echo esc_attr( $field[ 'key' ] ); ?>"></div>

		<?php
			// Display the rating hint.
			if ( $field[ 'show_hints' ] ) {
			?>
				<div class="memberhero-star-hint">
					<?php echo memberhero_get_rating_hint( absint( $field[ 'value' ] ), $field ); ?>
				</div>
			<?php
			}
		?>

		<?php do_action( 'memberhero_rating_field_output', $field ); ?>

	</div>

</fieldset>