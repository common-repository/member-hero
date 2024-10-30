<?php
/**
 * Modal: Edit row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="memberhero-modal modal-small" id="memberhero-edit-row">

	<h3><?php esc_html_e( 'Edit row', 'memberhero' ); ?></h3>

	<div class="modal-content memberhero_options_panel">
		<div class="options_group">
			<?php
				memberhero_wp_text_input(
					array(
						'id'          		=> 'row_title',
						'value'       		=> '',
						'type'				=> 'text',
						'placeholder'		=> __( 'Enter row title here...', 'memberhero' ),
						'label'       		=> esc_html__( 'Row title', 'memberhero' ),
						'description'		=> esc_html__( 'If you specify a title, It will appear in the front-end above all fields for this row.', 'memberhero' ),
					)
				);
			?>
		</div>
	</div>

	<div class="modal-footer">
		<a href="#" class="button button-primary save_row"><?php esc_html_e( 'Save changes', 'memberhero' ); ?></a>
		<a href="#" class="button button-secondary" rel="modal:close"><?php esc_html_e( 'Cancel', 'memberhero' ); ?></a>
	</div>

</div>