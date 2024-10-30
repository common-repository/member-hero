<?php
/**
 * Display form rows in a loop.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php for ( $row = 1; $row <= $the_form->row_count; $row++ ) : ?>

	<div class="memberhero-row">

		<?php if ( memberhero_has_row_title( $row ) ) : ?>
		<div class="memberhero-row-title"><?php echo esc_html( memberhero_get_row_title( $row ) ); ?></div>
		<?php endif; ?>

		<div class="memberhero-cols <?php echo implode( ' ', memberhero_get_column_classes( $row ) ); ?>" <?php memberhero_print_column_inline_styles( $row ); ?>>

			<?php for ( $col = 0; $col < $the_form->cols[$row]['count']; $col++ ) : ?>
				<?php
					memberhero_form_column(
						array(
							'row' 	=> $row,
							'col'	=> $col,
							'size'	=> $the_form->cols[$row]['layout'][$col]
						)
					);
				?>
			<?php endfor; ?>

		</div>
	</div>

<?php endfor; ?>

<?php do_action( 'memberhero_custom_' . esc_attr( $the_form->type ) . '_rows' ); ?>