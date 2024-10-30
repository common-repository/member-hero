<?php
/**
 * Image field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-data">

		<div class="memberhero-image">
			<a href="#" class="memberhero-modal-open" rel="memberhero-modal-view-image">
				<img src="<?php echo memberhero_generate_upload_url( $field[ 'value' ], $field[ 'type' ] ); ?>" alt="" />
				<span class="memberhero-image-overlay"></span>
				<span class="memberhero-image-icons">
					<span class="memberhero-image-view"><?php echo memberhero_svg_icon( 'eye' ); ?></span>
				</span>
			</a>
		</div>

		<?php do_action( 'memberhero_image_field_output', $field ); ?>

	</div>

</fieldset>