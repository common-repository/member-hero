<?php
/**
 * Modal: Add element to form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="memberhero-modal" id="memberhero-add-element">

	<h3><?php esc_html_e( 'Add field', 'memberhero' ); ?></h3>

	<div class="modal-content">

		<h4><?php esc_html_e( 'Existing custom fields', 'memberhero' ); ?></h4>
		<div class="memberhero-buttons">
			<p>
				<?php
				if ( ! empty( $fields = memberhero_get_custom_fields() ) ) {
					foreach( $fields as $key => $data ) {
						$label = empty( $data['label'] ) ? $key : $data['label'];
						?>
						<a href="#" class="button button-secondary insert_field" <?php echo memberhero_get_data_attributes( $data ); ?>>
							<?php echo esc_html( $label ); ?>
						</a>
						<?php
					}
				} else {
					?>
					<a href="#memberhero-create-fields" class="button button-primary">
						<?php echo esc_html( 'Create default custom fields', 'memberhero' ); ?>
					</a>
					<?php
				}
				?>
			</p>
		</div>

		<h4><?php esc_html_e( 'New field', 'memberhero' ); ?></h4>
		<div class="memberhero-buttons alt">
			<p>
				<?php foreach( memberhero_get_field_types() as $key => $data ) : if ( ! empty( $data[ 'misc' ] ) ) continue; ?>
					<a href="#memberhero-add-field" class="button button-primary new_field" data-type="<?php echo esc_attr( $key ); ?>" rel="modal:open">
						<?php echo memberhero_svg_icon( esc_attr( $data['icon'] ) ); ?>
						<?php echo esc_html( $data['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</p>
		</div>

		<?php if ( memberhero_misc_fields() ) : ?>
		<h4><?php esc_html_e( 'Misc fields', 'memberhero' ); ?></h4>
		<div class="memberhero-buttons alt">
			<p>
				<?php foreach( memberhero_misc_fields() as $key => $data ) : ?>
					<a href="#memberhero-add-field" class="button button-primary new_field" data-type="<?php echo esc_attr( $key ); ?>" rel="modal:open">
						<?php echo memberhero_svg_icon( esc_attr( $data['icon'] ) ); ?>
						<?php echo esc_html( $data['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</p>
		</div>
		<?php endif; ?>

	</div>

</div>