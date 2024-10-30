<?php
/**
 * File field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-data">

		<div class="memberhero-file">

			<?php if ( ! empty( $field[ 'delete_file' ] ) ) : ?>
			<div class="memberhero-file-actions">
				<div class="memberhero-file-delete memberhero-file-delete-confirm"><a href="#"><?php echo memberhero_svg_icon( 'x' ); ?><?php _e( 'Delete file', 'memberhero' ); ?></a></div>
				<div class="memberhero-file-delete memberhero-file-delete-options" style="display:none;">
					<a href="#" class="memberhero-file-delete-do memberhero-alert"><?php echo memberhero_svg_icon( 'x' ); ?><?php _e( 'Click to confirm delete', 'memberhero' ); ?></a> 
					<a href="#" class="memberhero-file-delete-undo"><?php echo memberhero_svg_icon( 'arrow-left' ); ?><?php _e( 'Back', 'memberhero' ); ?></a>
				</div>
			</div>
			<?php endif; ?>

			<a href="<?php echo memberhero_generate_upload_url( $field[ 'value' ], $field[ 'type' ] ); ?>" title="" class="memberhero-button nav">
				<span class="memberhero-file-icon">
					<?php echo memberhero_svg_icon( ! empty( $field[ 'icon' ] ) ? $field[ 'icon' ] : 'download' ); ?>
				</span>

				<span class="memberhero-file-name">
					<?php echo sprintf( __( 'Download: %s', 'memberhero' ), esc_attr( $field[ 'value' ] ) ); ?>
				</span>
			</a>
		</div>

		<?php do_action( 'memberhero_file_field_output', $field ); ?>

	</div>

</fieldset>