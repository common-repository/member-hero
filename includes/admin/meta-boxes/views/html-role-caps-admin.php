<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="admin_role_caps" class="panel memberhero_options_panel hidden">

	<div class="options_group">
		<?php
		$opts = array( 'memberhero_view_adminbar', 'memberhero_view_wpadmin' );
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
		$opts = array( 'memberhero_edit_users', 'memberhero_delete_users', 'memberhero_mod_users' );
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
		$opts = array( 'memberhero_settings', 'memberhero_addons', 'manage_memberhero' );
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

	<?php do_action( 'memberhero_role_caps_admin_panel' ); ?>

</div>