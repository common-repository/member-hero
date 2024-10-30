<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="general_field_data" class="panel memberhero_options_panel">

	<div class="options_group">
		<?php
		do_action( 'memberhero_before_general_field_options' );

		memberhero_wp_text_input(
			array(
				'id'          		=> 'key',
				'value'       		=> $the_field->key,
				'label'       		=> __( 'Unique key', 'memberhero' ),
				'placeholder' 		=> _x( 'custom_field', 'placeholder', 'memberhero' ),
				'description' 		=> __( 'This is how the custom field will be identified in the system.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_select(
			array(
				'id'          		=> 'type',
				'value'       		=> $the_field->type,
				'label'       		=> __( 'Custom field type', 'memberhero' ),
				'options'     		=> array_combine( array_keys( memberhero_get_field_types() ), array_column( memberhero_get_field_types(), 'label' ) ),
				'description' 		=> __( 'Controls how this custom field should be filled by the user.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		// Email
		memberhero_wp_textarea(
			array(
				'id'          		=> 'blocked_emails',
				'value'       		=> $the_field->blocked_emails,
				'label'       		=> __( 'Blocked emails', 'memberhero' ),
				'placeholder' 		=> 'you@domain.com' . "\n" . '*@domain.com' . "\n" . 'domain.com',
				'rows'				=> 5,
				'description'		=> __( 'Use this option if you want to block specific domains/emails from being used as email.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_type_eq_email hidden',
			)
		);

		memberhero_wp_textarea(
			array(
				'id'          		=> 'allowed_emails',
				'value'       		=> $the_field->allowed_emails,
				'label'       		=> __( 'Allowed emails', 'memberhero' ),
				'placeholder' 		=> 'you@domain.com' . "\n" . '*@domain.com' . "\n" . 'domain.com',
				'rows'				=> 5,
				'description'		=> __( 'Use this option if you want to allow specific domains/emails only to be used as email.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_type_eq_email hidden',
			)
		);

		// Textarea
		memberhero_wp_switch(
			array(
				'id'        		=> 'emojis',
				'label'				=> __( 'Enable emoji picker?', 'memberhero' ),
				'value'				=> $the_field->emojis,
				'cbvalue'			=> 1,
				'description'		=> __( 'If enabled, the user will be able to pick and insert emojies in this textarea.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_type_eq_textarea hidden',
			)
		);

		memberhero_wp_switch(
			array(
				'id'        		=> 'autolinks',
				'label'				=> __( 'Auto format links', 'memberhero' ),
				'value'				=> $the_field->autolinks,
				'cbvalue'			=> 1,
				'description'		=> __( 'If enabled, urls will be converted to clickable links automatically.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_type_eq_textarea hidden',
			)
		);

		// Number
		memberhero_wp_switch(
			array(
				'id'        		=> 'enable_decimals',
				'label'				=> __( 'Allow decimal numbers', 'memberhero' ),
				'value'				=> $the_field->enable_decimals,
				'cbvalue'			=> 1,
				'description'		=> __( 'If enabled, a user can put a number like this 5.25', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_type_eq_number hidden',
			)
		);

		memberhero_wp_text_input(
			array(
				'id'          		=> 'min_num',
				'value'       		=> $the_field->min_num,
				'label'       		=> __( 'Minimum number', 'memberhero' ),
				'description' 		=> __( 'The minimum number a user can put in this field', 'memberhero' ),
				'desc_tip'			=> true,
				'style'				=> 'width:60px;',
				'wrapper_class' 	=> 'show_if_type_eq_number hidden',
			)
		);

		memberhero_wp_text_input(
			array(
				'id'          		=> 'max_num',
				'value'       		=> $the_field->max_num,
				'label'       		=> __( 'Maximum number', 'memberhero' ),
				'description' 		=> __( 'The maximum number a user can put in this field', 'memberhero' ),
				'desc_tip'			=> true,
				'style'				=> 'width:60px;',
				'wrapper_class' 	=> 'show_if_type_eq_number hidden',
			)
		);

		// Select
		memberhero_wp_textarea(
			array(
				'id'          		=> 'dropdown_options',
				'value'       		=> $the_field->dropdown_options,
				'label'       		=> __( 'Dropdown options list', 'memberhero' ),
				'placeholder' 		=> __( 'Please enter one option per line.', 'memberhero' ),
				'rows'				=> 5,
				'wrapper_class' 	=> 'show_if_type_eq_select hidden',
			)
		);

		// Checkbox
		memberhero_wp_textarea(
			array(
				'id'          		=> 'checkbox_options',
				'value'       		=> $the_field->checkbox_options,
				'label'       		=> __( 'Checkbox options list', 'memberhero' ),
				'placeholder' 		=> __( 'Please enter one option per line.', 'memberhero' ),
				'rows'				=> 5,
				'wrapper_class' 	=> 'show_if_type_eq_checkbox hidden',
			)
		);

		// Radio
		memberhero_wp_textarea(
			array(
				'id'          		=> 'radio_options',
				'value'       		=> $the_field->radio_options,
				'label'       		=> __( 'Radio options list', 'memberhero' ),
				'placeholder' 		=> __( 'Please enter one option per line.', 'memberhero' ),
				'rows'				=> 5,
				'wrapper_class' 	=> 'show_if_type_eq_radio hidden',
			)
		);

		// File
		memberhero_wp_select(
			array(
				'id'          		=> 'mimes',
				'value'       		=> $the_field->mimes,
				'label'       		=> __( 'Accepted File Types', 'memberhero' ),
				'options'     		=> get_allowed_mime_types(),
				'placeholder'		=> __( 'Use WordPress defaults', 'memberhero' ),
				'description' 		=> __( 'If you want to allow specific file types for this upload.', 'memberhero' ),
				'desc_tip'			=> true,
				'custom_attributes' => array( 'multiple' => 'multiple' ),
				'wrapper_class' 	=> 'show_if_type_eq_file hidden',
			)
		);

		memberhero_wp_switch(
			array(
				'id'        		=> 'delete_file',
				'label'				=> __( 'Allow user to delete?', 'memberhero' ),
				'value'				=> $the_field->delete_file ? $the_field->delete_file : 'yes',
				'description'		=> __( 'If enabled, a user can delete a file they uploaded.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_type_eq_file hidden',
			)
		);

		// Rating
		memberhero_wp_switch(
			array(
				'id'        		=> 'show_hints',
				'label'				=> __( 'Show rating hints', 'memberhero' ),
				'value'				=> $the_field->show_hints,
				'cbvalue'			=> 1,
				'description'		=> __( 'Displays a hint for each rating score.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_type_eq_rating hidden',
			)
		);

		memberhero_wp_text_input(
			array(
				'id'          		=> 'ratings',
				'value'       		=> $the_field->ratings,
				'label'       		=> __( 'Rating hints', 'memberhero' ),
				'placeholder' 		=> __( 'Poor, Below average, Average, Good, Excellent', 'memberhero' ),
				'description' 		=> __( 'Enter a comma separated list for rating hints.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_type_eq_rating hidden',
			)
		);

		// Hook for 3rd party.
		do_action( 'memberhero_after_general_field_options' );
		?>
	</div>

	<?php do_action( 'memberhero_field_data_general_panel' ); ?>

</div>