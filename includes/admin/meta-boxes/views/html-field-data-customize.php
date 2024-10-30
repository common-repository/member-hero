<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="customize_field_data" class="panel memberhero_options_panel hidden">

	<div class="options_group">
		<?php
		memberhero_wp_text_input(
			array(
				'id'          		=> 'edit_label',
				'value'       		=> $the_field->edit_label,
				'label'       		=> __( 'Label (Edit)', 'memberhero' ),
				'placeholder' 		=> __( 'Leave blank for default', 'memberhero' ),
				'description' 		=> __( 'This is the label that is displayed when user is editing.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_text_input(
			array(
				'id'          		=> 'view_label',
				'value'       		=> $the_field->view_label,
				'label'       		=> __( 'Label (View)', 'memberhero' ),
				'placeholder' 		=> __( 'Leave blank for default', 'memberhero' ),
				'description' 		=> __( 'This is the label that is displayed when user is viewing.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_text_input(
			array(
				'id'          		=> 'icon',
				'value'       		=> $the_field->icon,
				'label'       		=> __( 'Icon', 'memberhero' ),
				'description' 		=> __( 'For full list of icons please check <a href="https://feathericons.com/">https://feathericons.com</a>', 'memberhero' ),
				'desc_tip'			=> true,
				'style'				=> 'width:100px;',
			)
		);

		memberhero_wp_text_input(
			array(
				'id'          		=> 'placeholder',
				'value'       		=> $the_field->placeholder,
				'label'       		=> __( 'Placeholder', 'memberhero' ),
				'description' 		=> __( 'This text will appear in case the user does not give any input.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_textarea(
			array(
				'id'          		=> 'helper',
				'value'       		=> $the_field->helper,
				'label'       		=> __( 'Help / Instructions', 'memberhero' ),
				'rows'				=> 3,
				'description'		=> __( 'Use this If you want to instruct the user how to fill the field.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_text_input(
			array(
				'id'          		=> 'custom_error',
				'value'       		=> $the_field->custom_error,
				'label'       		=> __( 'Custom Error', 'memberhero' ),
				'description' 		=> __( 'For example if the field was not filled correctly you can input your custom error here.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);
		?>
	</div>

	<?php do_action( 'memberhero_field_data_customize_panel' ); ?>

</div>