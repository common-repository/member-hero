<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="properties_field_data" class="panel memberhero_options_panel hidden">

	<div class="options_group">
		<?php
		memberhero_wp_switch(
			array(
				'id'        		=> 'is_required',
				'label'				=> __( 'Make this field required?', 'memberhero' ),
				'value'				=> $the_field->is_required,
				'cbvalue'			=> 1,
			)
		);

		memberhero_wp_switch(
			array(
				'id'        		=> 'is_private',
				'label'				=> __( 'Private', 'memberhero' ),
				'value'				=> $the_field->is_private,
				'cbvalue'			=> 1,
				'description'		=> __( 'This will hide the field data from the public. e.g. to hide e-mail', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_switch(
			array(
				'id'        		=> 'is_readonly',
				'label'				=> __( 'Read Only', 'memberhero' ),
				'value'				=> $the_field->is_readonly,
				'cbvalue'			=> 1,
				'description'		=> __( 'Enable to prevent the user from editing this custom field', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);
		?>
	</div>

	<div class="options_group">
		<?php
		memberhero_wp_select(
			array(
				'id'          		=> 'can_view',
				'value'       		=> $the_field->can_view,
				'label'       		=> __( 'Who can view?', 'memberhero' ),
				'options'     		=> array_merge( array( '_none' => __( 'No one', 'memberhero' ), 'owner' => __( 'Owner', 'memberhero' ) ), memberhero_get_roles() ),
				'placeholder'		=> __( 'Everyone', 'memberhero' ),
				'description' 		=> __( 'Who can view this custom field.', 'memberhero' ),
				'desc_tip'			=> true,
				'custom_attributes' => array( 'multiple' => 'multiple' ),
			)
		);
		?>
	</div>

	<?php do_action( 'memberhero_field_data_properties_panel' ); ?>

</div>