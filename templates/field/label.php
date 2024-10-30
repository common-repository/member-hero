<?php
/**
 * Field Label
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( ! empty( $field[ 'label' ] ) ) : ?>

<div class="memberhero-label <?php echo esc_attr( implode( ' ', $field[ 'title_class' ] ) ); ?>">

	<?php if ( in_array( 'has-icon', $field[ 'title_class' ] ) ) : ?>

		<span class="memberhero-icon">
			<?php
				echo memberhero_svg_icon( esc_attr( $field[ 'icon' ] ) );
			?>
		</span>

	<?php endif; ?>

	<label class="<?php echo esc_attr( implode( ' ', $field[ 'label_class' ] ) ); ?>" 
		<?php if ( empty( $helper ) && memberhero_get_scope() != 'view' ) { ?>
			for="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?>"
		<?php } ?>
	>
		<?php
			esc_html_e( $field[ 'label' ], 'memberhero' );
		?>
	</label>

	<?php
		// Add custom stuff beside label.
		do_action( 'memberhero_after_label_' . $field[ 'type' ], $field );
		do_action( 'memberhero_after_label_' . $field[ 'type' ] . '_' . memberhero_get_scope(), $field );
	?>

</div>

<?php endif; ?>