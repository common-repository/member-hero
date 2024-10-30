<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="general_list_data" class="panel memberhero_options_panel">

	<div class="options_group">
		<?php
		memberhero_wp_text_input(
			array(
				'id'          		=> 'per_page',
				'value'       		=> $the_list->per_page ? $the_list->per_page : 12,
				'type'				=> 'number',
				'label'       		=> __( 'Number of users per page', 'memberhero' ),
				'description'		=> __( 'How many users should be displayed per page.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_switch(
			array(
				'id'        		=> 'login_required',
				'label'				=> __( 'Require users to login to view', 'memberhero' ),
				'value'				=> $the_list->login_required,
				'cbvalue'			=> 1,
				'description'		=> __( 'If enabled, guests will be required to login to view this member directory.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		/*
		memberhero_wp_switch(
			array(
				'id'        		=> 'use_ajax',
				'label'				=> __( 'Show results with ajax', 'memberhero' ),
				'value'				=> $the_list->use_ajax,
				'description'		=> __( 'If enabled, new results will be displayed without refreshing the page.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);
		*/
		?>
	</div>

	<div class="options_group">
		<?php
		memberhero_wp_select(
			array(
				'id'          		=> 'roles',
				'value'       		=> $the_list->roles,
				'label'       		=> __( 'Who to show in this list?', 'memberhero' ),
				'options'     		=> array_merge( array( '_all' => __( 'Everyone excluding admins', 'memberhero' ) ), memberhero_get_roles() ),
				'placeholder'		=> __( 'Everyone', 'memberhero' ),
				'description' 		=> __( 'This controls which user groups can be shown in this specific list.', 'memberhero' ),
				'desc_tip'			=> true,
				'custom_attributes' => array( 'multiple' => 'multiple' ),
			)
		);
		?>
	</div>

	<div class="options_group">
		<?php
		memberhero_wp_select(
			array(
				'id'          		=> 'orderby',
				'value'       		=> $the_list->orderby ? $the_list->orderby : 'date_desc',
				'label'       		=> __( 'Default order', 'memberhero' ),
				'options'     		=> memberhero_get_sorting_options(),
				'description' 		=> __( 'This is the default order in which members in this member directory will be displayed', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);
		?>
	</div>

	<?php do_action( 'memberhero_list_data_general_panel' ); ?>

</div>