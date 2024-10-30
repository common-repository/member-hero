<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="general_role_caps" class="panel memberhero_options_panel">

	<div class="options_group">
		<?php
		$opts = array( 'memberhero_edit_profile', 'memberhero_edit_account', 'memberhero_delete_account' );
		foreach( $opts as $opt ) {
			memberhero_wp_switch(
				array(
					'id'        	=> $opt,
					'label'			=> memberhero_get_cap_title( $opt ),
					'value'			=> $the_role->get_cap( $opt ),
					'cbvalue'		=> 1
				)
			);
		}
		?>
	</div>

	<div class="options_group">
		<?php
		$opts = array( 'memberhero_view_profiles', 'memberhero_view_list', 'memberhero_search_list' );
		foreach( $opts as $opt ) {
			memberhero_wp_switch(
				array(
					'id'        	=> $opt,
					'label'			=> memberhero_get_cap_title( $opt ),
					'value'			=> $the_role->get_cap( $opt ),
					'cbvalue'		=> 1
				)
			);
		}
		?>
	</div>

	<div class="options_group">
		<?php
		$opts = array( 'memberhero_view_private', 'memberhero_view_private_data' );
		foreach( $opts as $opt ) {
			memberhero_wp_switch(
				array(
					'id'        	=> $opt,
					'label'			=> memberhero_get_cap_title( $opt ),
					'value'			=> $the_role->get_cap( $opt ),
					'cbvalue'		=> 1
				)
			);
		}
		?>
	</div>

	<?php do_action( 'memberhero_role_caps_general_panel' ); ?>

</div>