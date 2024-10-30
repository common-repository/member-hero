<?php
/**
 * Rating field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field[ 'field_class' ] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-input <?php echo implode( ' ', $field[ 'control_class' ] ); ?>">

		<div class="memberhero-star" 
			data-key="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field[ 'key' ] ); ?>" 
			data-score="<?php echo absint( $field[ 'value' ] ); ?>" 
			data-ratings="<?php echo memberhero_get_rating_hints( $field ); ?>" >
		</div>

		<?php if ( $field[ 'show_hints' ] ) : ?>
			<div class="memberhero-star-hint">
				<?php
					if ( $field[ 'value' ] > 0 ) {
						echo memberhero_get_rating_hint( $field[ 'value' ], $field );
					}
				?>
			</div>
		<?php endif; ?>

	</div>

	<?php if ( ! empty( $field[ 'helper' ] ) ) : ?>
		<div class="memberhero-helper"><?php echo wp_kses_post( $field[ 'helper' ] ); ?></div>
	<?php endif; ?>

</fieldset>