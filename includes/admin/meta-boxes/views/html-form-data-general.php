<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="general_form_data" class="panel memberhero_options_panel">

	<div class="options_group">
		<?php
		memberhero_wp_select(
			array(
				'id'          		=> '_type',
				'value'       		=> $the_form->type,
				'label'       		=> __( 'Form type', 'memberhero' ),
				'options'     		=> array_combine( array_keys( memberhero_get_form_types() ), array_column( memberhero_get_form_types(), 'label' ) ),
				'description' 		=> __( 'Choose a type for this form.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_select(
			array(
				'id'          		=> 'endpoint',
				'value'       		=> $the_form->endpoint,
				'label'       		=> __( 'Form Endpoint', 'memberhero' ),
				'options'     		=> array_diff_key( array_merge( array( '' => __( 'Unassigned', 'memberhero' ) ), memberhero_get_account_menu_items() ), array_flip( array( 'logout' ) ) ),
				'description' 		=> __( 'For example, account page has multiple endpoints. Each form can be linked to a specific endpoint.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if__type_eq_account hidden',
			)
		);

		if ( ! is_memberhero_default_form( $the_form ) ) {
			memberhero_wp_switch(
				array(
					'id'        		=> 'set_default',
					'label'				=> __( 'Set as default form', 'memberhero' ),
					'description'		=> __( 'Enable this if you want to set this form as default form.', 'memberhero' ),
					'desc_tip'			=> true,
				)
			);
		}

		memberhero_wp_switch(
			array(
				'id'        		=> 'use_ajax',
				'label'				=> __( 'Use AJAX', 'memberhero' ),
				'value'				=> $the_form->use_ajax,
				'description'		=> __( 'If enabled, this form will interact instantly without page refresh.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_select(
			array(
				'id'          		=> 'icons',
				'value'       		=> $the_form->icons,
				'label'       		=> __( 'Show field icons?', 'memberhero' ),
				'options'     		=> array(
					'hide'		=> __( 'Do not show', 'memberhero' ),
					'label'		=> __( 'Beside label', 'memberhero' ),
					'inside'	=> __( 'Inside field (if possible)', 'memberhero' ),
				),
				'description' 		=> __( 'If enabled, field icons will be displayed in the form.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);
		?>
	</div>

	<div class="options_group">
		<?php
		memberhero_wp_switch(
			array(
				'id'          		=> 'force_role',
				'label'				=> __( 'Force a specific role?', 'memberhero' ),
				'value'				=> $the_form->force_role,
				'description'		=> __( 'If checked, this form will only be linked to the specified role below. No other role will be able to view or use this form.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_select(
			array(
				'id'          		=> 'role',
				'value'       		=> $the_form->role,
				'label'       		=> __( 'Select a role', 'memberhero' ),
				'options'     		=> memberhero_get_roles(),
				'description' 		=> __( 'This form will be linked to selected role only.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_force_role_eq_yes hidden',
			)
		);
		?>
	</div>

	<div class="options_group show_if__type_eq_register hidden">
		<?php
		memberhero_wp_switch(
			array(
				'id'          		=> 'confirm_email',
				'label'				=> __( 'Add confirm email field', 'memberhero' ),
				'value'				=> $the_form->confirm_email,
				'description'		=> __( 'Adds email confirmation field to registration form.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_switch(
			array(
				'id'          		=> 'confirm_password',
				'label'				=> __( 'Add confirm password field', 'memberhero' ),
				'value'				=> $the_form->confirm_password,
				'description'		=> __( 'Adds password confirmation field to registration form.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);
		?>
	</div>

	<div class="options_group show_if__type_eq_profile hidden">
		<?php
		memberhero_wp_switch(
			array(
				'id'          		=> 'show_menu',
				'label'				=> __( 'Show profile menu', 'memberhero' ),
				'value'				=> $the_form->show_menu ? $the_form->show_menu : 'yes',
				'description'		=> __( 'The main profile menu is enabled by default.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_switch(
			array(
				'id'          		=> 'show_members_menu',
				'label'				=> __( 'Show members link in menu', 'memberhero' ),
				'value'				=> $the_form->show_members_menu ? $the_form->show_members_menu : 'yes',
				'description'		=> __( 'Display a link to the member directory in profile menu.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_switch(
			array(
				'id'          		=> 'show_cover',
				'label'				=> __( 'Show header photos', 'memberhero' ),
				'value'				=> $the_form->show_cover ? $the_form->show_cover : 'yes',
				'description'		=> __( 'The header photos are enabled by default.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_switch(
			array(
				'id'          		=> 'show_social',
				'label'				=> __( 'Show social profiles', 'memberhero' ),
				'value'				=> $the_form->show_social ? $the_form->show_social : 'yes',
				'description'		=> __( 'Will display user social profiles If they are filled.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_switch(
			array(
				'id'          		=> 'aligncenter',
				'label'				=> __( 'Centrally align profile info', 'memberhero' ),
				'value'				=> $the_form->aligncenter,
				'description'		=> __( 'Enable this to align profile info and avatar to the center.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);
		?>
	</div>

	<?php do_action( 'memberhero_form_data_general_panel' ); ?>

</div>