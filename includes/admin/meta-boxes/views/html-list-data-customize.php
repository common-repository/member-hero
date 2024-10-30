<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="customize_list_data" class="panel memberhero_options_panel">

	<div class="options_group">
		<?php
		memberhero_wp_switch(
			array(
				'id'          		=> 'show_menu',
				'label'				=> __( 'Show profile menu', 'memberhero' ),
				'value'				=> $the_list->show_menu ? $the_list->show_menu : 'yes',
				'description'		=> __( 'The profile menu is displayed by default.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_switch(
			array(
				'id'          		=> 'show_social',
				'label'				=> __( 'Show social profiles', 'memberhero' ),
				'value'				=> $the_list->show_social ? $the_list->show_social : 'yes',
				'description'		=> __( 'Will display user social profiles If they are filled.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_switch(
			array(
				'id'          		=> 'show_bio',
				'label'				=> __( 'Show user description', 'memberhero' ),
				'value'				=> $the_list->show_bio ? $the_list->show_bio : 'yes',
				'description'		=> __( 'If turned off, the user description will not appear in members directory.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);
	
		memberhero_wp_switch(
			array(
				'id'          		=> 'centered',
				'label'				=> __( 'Align card contents to center?', 'memberhero' ),
				'value'				=> $the_list->centered,
				'description'		=> __( 'If enabled, the member list card content will be centrally aligned.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);
		?>
	</div>

	<?php do_action( 'memberhero_list_data_customize_panel' ); ?>

</div>