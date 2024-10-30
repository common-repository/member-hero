<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="general_role_data" class="panel memberhero_options_panel">

	<div class="options_group">
		<?php
		memberhero_wp_switch(
			array(
				'id'        		=> 'bypass_globals',
				'label'				=> __( 'Override registration action', 'memberhero' ),
				'value'				=> $the_role->bypass_globals,
				'description'		=> __( 'Enable this to override global options for this role.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_switch(
			array(
				'id'        		=> 'email_confirm',
				'label'				=> __( 'Email confirmation', 'memberhero' ),
				'value'				=> $the_role->email_confirm,
				'description'		=> __( 'Users registering with this role will require email confirmation.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_bypass_globals_eq_yes hidden',
			)
		);

		memberhero_wp_switch(
			array(
				'id'        		=> 'manual_approval',
				'label'				=> __( 'Admin approval', 'memberhero' ),
				'value'				=> $the_role->manual_approval,
				'description'		=> __( 'Users registering with this role will require admin approval.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_bypass_globals_eq_yes hidden',
			)
		);
		?>
	</div>

	<?php do_action( 'memberhero_role_data_general_panel' ); ?>

</div>